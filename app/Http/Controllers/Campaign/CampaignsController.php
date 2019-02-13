<?php

namespace Vanguard\Http\Controllers\Campaign;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Controller;
use Session;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\Campaigns\CampaignGeneralInformationRequest;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\CampaignDate;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Adslot;
use Vanguard\Models\PreselectedAdslot;
use Vanguard\Models\Transaction;
use Vanguard\Models\Upload;
use Vanguard\Services\Adslot\AdslotFilterResult;
use Vanguard\Services\Adslot\PreselectedAdslotService;
use Vanguard\Services\Adslot\RatecardService;
use Vanguard\Services\Campaign\CampaignCardPayment;
use Vanguard\Services\Campaign\CampaignExtras;
use Vanguard\Services\Campaign\CampaignOnhold;
use Vanguard\Services\Campaign\DeleteTemporaryUpload;
use Vanguard\Services\Campaign\SingleCampaign;
use Vanguard\Services\Campaign\StoreCampaign;
use Vanguard\Services\Campaign\StoreCampaignDetails;
use Vanguard\Services\Campaign\StoreCampaignGeneralInformation;
use Vanguard\Services\Campaign\StoreInvoice;
use Vanguard\Services\Campaign\StoreInvoiceDetails;
use Vanguard\Services\Campaign\storeMpo;
use Vanguard\Services\Campaign\StoreMpoDetails;
use Vanguard\Services\Campaign\StorePayment;
use Vanguard\Services\Campaign\StorePaymentDetails;
use Vanguard\Services\Campaign\StoreSelectedAdslot;
use Vanguard\Services\Campaign\UpdateCampaignFromHoldState;
use Vanguard\Services\Client\AllClient;
use Vanguard\Services\Campaign\AllCampaign;
use Vanguard\Services\Client\ClientBrand;
use Vanguard\Services\Client\ClientDetails;
use Vanguard\Services\Company\CompanyDetails;
use Vanguard\Services\FilePosition\AdslotFilePositionService;
use Vanguard\Services\FilePosition\Fileposition;
use Vanguard\Services\Industry\IndustryAndSubindustry;
use Vanguard\Services\PreloadedData\PreloadedData;
use Vanguard\Services\Upload\MediaUploadProcessing;
use Vanguard\Services\Wallet\CreateWalletHistory;
use Vanguard\Services\Wallet\UpdateWallet;
use Vanguard\Services\Wallet\WelletService;
use Yajra\DataTables\DataTables;

class CampaignsController extends Controller
{
    protected $utilities;
    protected $dataTables;
    private $broadcaster_id;
    private $agency_id;
    private $campaign_general_information;
    protected $campaign_date;

    public function __construct(Utilities $utilities, DataTables $dataTables, CampaignDate $campaign_date)
    {
        $this->utilities = $utilities;
        $this->dataTables = $dataTables;
        $this->campaign_date = $campaign_date;
        $this->middleware(function ($request, $next) {
            $this->broadcaster_id = Session::get('broadcaster_id');
            $this->agency_id = Session::get('agency_id');
            $this->campaign_general_information = Session::get('campaign_information');
            return $next($request);
        });
    }

    public function allActiveCampaigns()
    {
        if($this->broadcaster_id){
            return view('broadcaster_module.campaigns.index');
        }else{
            return view('agency.campaigns.active_campaign');
        }
    }

    public function allActiveCampaignsData(Request $request)
    {
        $campaigns = new AllCampaign($request, $this->broadcaster_id, $this->agency_id, $dashboard = false, null);
        return $campaigns->run();
    }

    public function campaignGeneralInformation()
    {
        $preloaded_data = new PreloadedData();
        $client_brand = '';
        if($this->campaign_general_information){
            $client_brand = new ClientBrand($this->campaign_general_information->client);
            $client_brand = $client_brand->run();
        }
        $clients = new AllClient(\Auth::user()->companies->first()->id);
        $clients = $clients->getAllClients();
        return view('campaigns.campaign_general_information')
                ->with('industries', $preloaded_data->getSectors())
                ->with('day_parts', $preloaded_data->getDayParts())
                ->with('regions', $preloaded_data->getRegions())
                ->with('brands', $client_brand)
                ->with('clients', $clients)
                ->with('targets', $preloaded_data->getTargetAudiences())
                ->with('campaign_general_information', $this->campaign_general_information)
                ->with('channels', $preloaded_data->getCampaignChannels())
                ->with('sub_industries', $preloaded_data->getSubsectors());

    }

    public function storeCampaignGeneralInformation(CampaignGeneralInformationRequest $request)
    {
        if($request->min_age < 0 || $request->max_age < 0){
            Session::flash('error', ClassMessages::NEGATIVE_AGE_MESSAGE);
            return back();
        }elseif ($request->min_age > $request->max_age){
            Session::flash('error', ClassMessages::AGE_ERROR_MESSAGE);
            return back();
        }elseif (strtotime($request->end_date) < strtotime($request->start_date)){
            Session::flash('error', ClassMessages::DATE_ERROR_MESSAGE);
            return redirect()->back();
        }
        $campaign_general_information = new StoreCampaignGeneralInformation($request);
        $user_id = $campaign_general_information->run();
        $delete_temporary_uploads = new DeleteTemporaryUpload($this->broadcaster_id, $this->agency_id, $user_id);
        $delete_temporary_uploads->run();
        return redirect()->route('campaign.advert_slot', ['id' => $user_id])
                        ->with('id', $user_id);
    }

    public function getBrandsIndustryAndSubIndustry()
    {
        $brand_industry_subindustry = new IndustryAndSubindustry(request()->brand);
        $brand_industry_subindustry = $brand_industry_subindustry->getBrandIndustryAndSubIndustry();
        return response()->json($brand_industry_subindustry);
    }

    public function getAdSlotResult($id)
    {
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        return view('campaigns.advert_slot_result')
                    ->with('adslots_filter_result', $this->adslotResultDetails())
                    ->with('id', $id);

    }

    public function getMediaContent($id)
    {
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        if(count($this->adslotResultDetails()) === 0){
            Session::flash('error', ClassMessages::EMPTY_ADSLOT_RESULT_FROM_FILTER);
            return redirect()->back();
        }
        $tv_details_and_uploads = $this->utilities->getTvDetailsAndUploads($id);
        $radio_details_and_uploads = $this->utilities->getRadioDetailsAndUploads($id);
        //going by defensive programming
        if($radio_details_and_uploads['radio']->channel != 'Radio' && $tv_details_and_uploads['tv']->channel != 'TV'){
            Session::flash('error', ClassMessages::FIRST_CHANNEL_ERROR);
            return redirect()->back();
        }
        return view('campaigns.media_content')
                    ->with('id', $id)
                    ->with('broadcaster_details', $this->broadcaster_id ? \Auth::user()->companies->first() : '')
                    ->with('radio', $radio_details_and_uploads['radio'])
                    ->with('tv', $tv_details_and_uploads['tv'])
                    ->with('tv_uploads', $tv_details_and_uploads['tv_upload_details'])
                    ->with('radio_uploads', $radio_details_and_uploads['radio_upload_details'])
                    ->with('campaign_general_information', $this->campaign_general_information);
    }

    public function storeMediaContent($id)
    {
        $media_upload = new MediaUploadProcessing(request(), '', '');
        $media_upload = $media_upload->run();
        if($media_upload === 'error'){
            return response()->json(['error' => 'error']);
        }elseif($media_upload === 'error_number'){
            return response()->json(['error_number' => 'error_number']);
        }elseif($media_upload === 'error_check_image'){
            return response()->json(['error_check_image' => 'error_check_image']);
        }elseif($media_upload === 'success'){
            return response()->json(['success' => 'success']);
        }
    }

    public function removeMediaContent($client_id, $upload_id)
    {
        $delete_media = new MediaUploadProcessing('',$client_id, $upload_id);
        $delete_media = $delete_media->deleteUploadedMedia();
        if($delete_media == 'success'){
            Session::flash('success', ClassMessages::FILE_DELETE_SUCCESS_MESSAGE);
            return back();
        }else{
            Session::flash('error', ClassMessages::FILE_DELETE_ERROR_MESSAGE);
            return back();
        }
    }

    public function preProcessAdslot($id)
    {
        $first_week = $this->campaign_date->getFirstWeek($this->campaign_general_information->start_date, $this->campaign_general_information->end_date);
        $campaign_start_end_date_of_the_week = $this->campaign_date->getStartAndEndDateForFirstWeek($first_week);
        return redirect()->route('campaign.adslot_selection', ['id' => $id, 'broadcaster' => $this->broadcaster_id,
                                    'start_date' => $campaign_start_end_date_of_the_week['start_date_of_the_week'],
                                    'end_date' => $campaign_start_end_date_of_the_week['end_date_of_the_week']
                                ]);
    }

    public function getAdslotSelection($id, $broadcaster_id, $start_date, $end_date)
    {
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        $preselected_slots_service = new PreselectedAdslotService($id, null, null, null, null);
        $file_position_service = new Fileposition($broadcaster_id);
        $broadcaster_details_service = new CompanyDetails($broadcaster_id);
        $broadcaster_details = $broadcaster_details_service->getCompanyDetails();
        $campaign_week = $this->utilities->getStartAndEndDateWithTheWeek($this->campaign_general_information->start_date,
                                                                        $this->campaign_general_information->end_date);
        $media_details = $this->utilities->getMediaUploadDetails($id, null);
        $preselected_adslots = $preselected_slots_service->getPreselectedSlots();
        $total_price = $preselected_slots_service->getSumPrice();
        $file_positions = $file_position_service->filePositionDetails();
        $rate_card_object = new RatecardService($this->campaign_general_information,$start_date,$end_date,
            $broadcaster_details->channels->first()->id, $broadcaster_details->id);
        $rate_cards = $rate_card_object->run();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($rate_cards['rate_cards']);
        $perPage = 100;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('/campaign/adslot-selection/'.$id.'/'.$this->broadcaster_id);

        //when dealing with multiple broadcasters
        $adslot_results = new AdslotFilterResult($this->campaign_general_information,
                                $this->campaign_general_information->start_date,$this->campaign_general_information->end_date);
        $campaign_first_week = $this->campaign_date->getFirstWeek($this->campaign_general_information->start_date,
                                $this->campaign_general_information->end_date);
        $campaign_start_date = current($campaign_first_week);
        $campaign_end_date = end($campaign_first_week);

        return view('campaigns.adslot_selection')
            ->with('ratecards', $entries)
            ->with('total_amount', $total_price)
            ->with('preselected_adslots', $preselected_adslots)
            ->with('uploaded_data', $media_details)
            ->with('id', $id)
            ->with('broadcaster', $broadcaster_id)
            ->with('positions', $file_positions)
            ->with('ratings', $rate_cards['adslots'])
            ->with('campaign_dates_by_week', $campaign_week)
            ->with('campaign_general_information', $this->campaign_general_information)
            ->with('adslot_search_results', $adslot_results->adslotFilterResult())
            ->with('campaign_start_date', $campaign_start_date)
            ->with('campaign_end_date', $campaign_end_date);

    }

    public function postPreselectedAdslot($id)
    {
        $post_preselected_adslot = new PreselectedAdslotService($id, request()->broadcaster, $this->agency_id, request(),
                                                                $this->campaign_general_information);
        $post_preselected_adslot = $post_preselected_adslot->storePreselectedAdslot();
        if($post_preselected_adslot === 'file_error'){
            return response()->json(['file_error' => 'file_error']);
        }elseif($post_preselected_adslot === 'budget_exceed_error'){
            return response()->json(['budget_exceed_error' => 'budget_exceed_error']);
        }elseif($post_preselected_adslot === 'error'){
            return response()->json(['error' => 'error']);
        }elseif(!$post_preselected_adslot){
            return response()->json(['failure' => 'failure']);
        }else{
            return response()->json(['success' => 'success']);
        }
    }

    public function selectBroadcaster($id)
    {
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        $adslot_results = new AdslotFilterResult($this->campaign_general_information,$this->campaign_general_information->start_date,
                                                        $this->campaign_general_information->end_date);
        $campaign_first_week = $this->campaign_date->getFirstWeek($this->campaign_general_information->start_date,
                                                                    $this->campaign_general_information->end_date);
        $start_date = current($campaign_first_week);
        $end_date = end($campaign_first_week);
        return view('campaigns.broadcaster_select')
                    ->with('adslot_search_results', $adslot_results->adslotFilterResult())
                    ->with('id', $id)
                    ->with('start_date', $start_date)
                    ->with('end_date', $end_date);
    }

    public function checkout($id)
    {
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }

        $preselected_adslot_object = new PreselectedAdslotService($id, $this->broadcaster_id,$this->agency_id, null, null);
        $count_preselected_adslot = $preselected_adslot_object->countPreselectedAdslot();
        if($count_preselected_adslot == 0){
            Session::flash('error', ClassMessages::EMPTY_CART_MESSAGE);
            return redirect()->route('campaign.broadcaster_select', ['id' => $id]);
        }
        $campaign_weeks = $this->campaign_date->groupCampaignDateByWeek($this->campaign_general_information->start_date,
                                                                        $this->campaign_general_information->end_date);
        $first_week = array_first($campaign_weeks);

        $wallet = new WelletService($this->agency_id);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($preselected_adslot_object->runPreselectedAdslotDetails());
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath($id);

        return view('campaigns.checkout')
                ->with('total_spent', $preselected_adslot_object->sumTotalPriceByMediaBuyer())
                ->with('preselected_adslot_arrays', $entries)
                ->with('id', $id)
                ->with('broadcaster', $this->broadcaster_id)
                ->with('campaign_dates_for_first_week', $first_week)
                ->with('wallet_balance', $wallet->getCurrentBalance());

    }

    public function removePreselectedAdslot($id)
    {
        PreselectedAdslot::where('id', $id)->delete();
        Session::flash('success', ClassMessages::REMOVE_PRESELECTED_ADSLOT);
        return redirect()->back();
    }

    public function postCampaign($user_id)
    {
        $save_campaign = $this->postCampaignOnHold($user_id);
        if($save_campaign === 'success'){
            $description = 'Campaign created by '.Session::get('broadcaster_id').' for '.$user_id;
            Api::saveActivity($user_id, $description);
            Session::flash('success', ClassMessages::CAMPAIGN_SUCCESS_MESSAGE);
            if($this->broadcaster_id){
                return redirect()->route('broadcaster.campaign.hold');
            }else{
                return redirect()->route('agency.campaigns.hold');
            }
        }else{
            Session::flash('error', ClassMessages::CAMPAIGN_ERROR_MESSAGE);
            return redirect()->back();
        }

    }

    public function getCampaignOnHold()
    {
        $campaigns_onhold = new CampaignOnhold($this->broadcaster_id, $this->agency_id);
        $campaigns_onhold = $campaigns_onhold->getCampaignsOnhold();
        $campaigns = Utilities::getCampaignDatatablesforCampaignOnHold($campaigns_onhold);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($campaigns);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $campaigns = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $campaigns->setPath('data');
        if($this->broadcaster_id){
            return view('campaigns.broadcaster.campaign_onhold', compact('campaigns'));
        }else{
            return view('campaigns.agency.campaign_onhold', compact('campaigns'));
        }
    }

    public function postCampaignOnHold($user_id)
    {
        $campaign_id = uniqid();
        $mpo_id = uniqid();
        $payment_id = uniqid();
        $invoice_id = uniqid();
        $campaign_reference = Utilities::generateReference();
        $invoice_number = Utilities::generateReference();
        $now = strtotime(Carbon::now('Africa/Lagos'));
        $client_details_object = new ClientDetails(null, $user_id);
        $client_details = $client_details_object->run();
        if($this->broadcaster_id){
            $broadcaster_campaign = $this->broadcasterCampaignOnHold($campaign_id, $mpo_id, $payment_id, $invoice_id, $campaign_reference,
                                                $invoice_number, $now, $user_id, $client_details);
            if($broadcaster_campaign == 'error'){
                Session::flash('error', ClassMessages::CAMPAIGN_ERROR_MESSAGE);
                return redirect()->back();
            }elseif ($broadcaster_campaign == 'success'){
                Session::forget('campaign_information');
                return 'success';
            }
            else{
                Session::flash('info', $broadcaster_campaign);
                return redirect()->back();
            }
        }else{
            $agency_campaign = $this->agencyCampaignOnHold($campaign_id, $mpo_id, $payment_id, $invoice_id, $campaign_reference,
                                            $invoice_number, $now, $user_id, $client_details);
            if($agency_campaign == 'insufficient_fund'){
                Session::flash('error', ClassMessages::INSUFFICIENT_FUND);
                return redirect()->back();
            }else if($agency_campaign == 'wallet_not_exist'){
                Session::flash('error', ClassMessages::WALLET_NOT_EXIST);
                return redirect()->back();
            }else if($agency_campaign == 'success'){
                Session::forget('campaign_information');
                return 'success';
            }
            else {
                \Session::flash('info', $agency_campaign);
                return back();
            }
        }
    }

    public function submitWithCardPaymentOption(Request $request)
    {
        $campaign_extras = new CampaignExtras($request->campaign_id, $this->broadcaster_id, $this->agency_id);
        $check_start_date = $campaign_extras->checkStartDateAgainstCurrentDate();
        if($check_start_date == 'error'){
            Session::flash('error', ClassMessages::START_DATE_ERROR);
            return redirect()->back();
        }
        $payment_method = 'Card';
        $save_campaign_with_card_option = new CampaignCardPayment($request->user_id, $request->reference, $request->total, $request->campaign_id,
                                                $payment_method, $this->broadcaster_id, $this->agency_id);
        $save_campaign = $save_campaign_with_card_option->processCampaignWithPaystack();
        if($save_campaign === 'success'){
            $description = 'Campaign created by '.Session::get('broadcaster_id').' for successfully';
            Api::saveActivity(Session::get('broadcaster_id'), $description);
            Session::flash('success', ClassMessages::CAMPAIGN_SUBMIT_TO_BROADCASTER_SUCCESS);
            return redirect()->route('broadcaster.campaign_management');
        }else{
            Session::flash('error', ClassMessages::CAMPAIGN_SUBMIT_TO_BROADCASTER_ERROR);
            return redirect()->back();
        }
    }

    public function submitWithOtherPaymentOption(Request $request, $campaign_id)
    {
        $campaign_extras = new CampaignExtras($campaign_id, $this->broadcaster_id, $this->agency_id);
        $check_start_date = $campaign_extras->checkStartDateAgainstCurrentDate();
        if($check_start_date == 'error'){
            Session::flash('error', ClassMessages::START_DATE_ERROR);
            return redirect()->back();
        }        $update_campaign_from_hold_state = new UpdateCampaignFromHoldState($campaign_id, $request->payment_option, $this->broadcaster_id, $this->agency_id);
        $save_campaign = $update_campaign_from_hold_state->updateCampaignInformation();
        if($save_campaign === 'success'){
            $description = 'Campaign created by '.Session::get('broadcaster_id').' for successfully';
            Api::saveActivity(Session::get('broadcaster_id'), $description);
            Session::flash('success', ClassMessages::CAMPAIGN_SUBMIT_TO_BROADCASTER_SUCCESS);
            return redirect()->route('broadcaster.campaign_management');
        }else{
            Session::flash('error', ClassMessages::CAMPAIGN_ERROR_MESSAGE);
            return redirect()->back();
        }
    }

    public function submitAgencyCampaign($campaign_id)
    {
        $campaign_extras = new CampaignExtras($campaign_id, $this->broadcaster_id, $this->agency_id);
        $check_start_date = $campaign_extras->checkStartDateAgainstCurrentDate();
        if($check_start_date == 'error'){
            Session::flash('error', ClassMessages::START_DATE_ERROR);
            return redirect()->back();
        }
        $wallet_balance_service = new WelletService($this->agency_id);
        $wallet_balance = $wallet_balance_service->getCurrentBalance();
        $single_campaign = new SingleCampaign($campaign_id, null, $this->agency_id);
        $single_campaign = $single_campaign->getSingleCampaign();
        if($wallet_balance->current_balance < $single_campaign->total){
            Session::flash('error', ClassMessages::INSUFFICIENT_FUND);
            return redirect()->back();
        }
        $wallet_new_balance = $wallet_balance->current_balance - $single_campaign->total;
        $payment_method = ClassMessages::WALLET_PAYMENT_METHOD;
        $update_campaign_from_hold_state = new UpdateCampaignFromHoldState($campaign_id, $payment_method, null, $this->agency_id);
        try{
            Utilities::switch_db('api')->transaction(function () use ($update_campaign_from_hold_state, $single_campaign, $wallet_new_balance, $wallet_balance) {
                $update_campaign_from_hold_state->updateCampaignInformation();
                $transaction = new Transaction();
                $transaction->id = uniqid();
                $transaction->amount = $single_campaign->total;
                $transaction->user_id = $this->agency_id;
                $transaction->reference = $single_campaign->invoice_id;
                $transaction->ip_address = request()->ip();
                $transaction->type = ClassMessages::DEBIT_TRANSACTION_TYPE;
                $transaction->message = ClassMessages::DEBIT_MESSAGE;
                $transaction->save();

                $wallet_histories = new CreateWalletHistory($this->agency_id, $single_campaign->total, $wallet_new_balance, $wallet_balance->current_balance);
                $wallet_histories->createHistory();

                $update_wallet = new UpdateWallet($this->agency_id, $wallet_balance->current_balance, $wallet_new_balance);
                $update_wallet->updateWallet();
            });
        }catch (\Exception $exception){
            Session::flash('error', ClassMessages::CAMPAIGN_SUBMIT_TO_BROADCASTER_ERROR);
            return redirect()->back();
        }
        Session::flash('success', ClassMessages::CAMPAIGN_SUBMIT_TO_BROADCASTER_SUCCESS);
        return redirect()->route('dashboard');
    }

    public function broadcasterCampaignOnHold($campaign_id, $mpo_id, $payment_id, $invoice_id, $campaign_reference, $invoice_number, $now, $user_id, $client_details)
    {
        $preselected_adslot_object = new PreselectedAdslotService($user_id, $this->broadcaster_id, null,
            null,null);
        $preselected_adslots = $preselected_adslot_object->getPreselectedSlots();
        $adslot_ids = $preselected_adslot_object->getAdslotIdFromPreselectedAdslot();
        $total_spent = $preselected_adslot_object->sumTotalPriceGroupedByBroadcaster();
        $broadcaster_details = new CompanyDetails($this->broadcaster_id);
        $broadcaster_details = $broadcaster_details->getCompanyDetails();
        $store_campaign = new StoreCampaign($campaign_id, $now, $campaign_reference);
        $store_mpo = new storeMpo($mpo_id, $campaign_id, $invoice_number, $campaign_reference);
        $store_invoice = new StoreInvoice($invoice_id, $campaign_id, $campaign_reference, $payment_id, $invoice_number);
        $store_payment = new StorePayment($payment_id, $campaign_id, $total_spent, $now, $campaign_reference, $this->campaign_general_information->campaign_budget);
        $post_campaign_bank = $this->collectInformationNeeded($store_campaign, $store_mpo, $store_invoice, $store_payment, $preselected_adslots,
            $campaign_id, $invoice_id, $mpo_id, $payment_id, $invoice_number, $adslot_ids, $total_spent, $user_id, $campaign_reference);
        $check_time_adslots = $this->fetchTimeRemainders($user_id, $this->broadcaster_id);
        foreach ($check_time_adslots as $check_time_adslot){
            if($check_time_adslot['initial_time_left'] < $check_time_adslot['time_bought']){
                $msg = 'You cannot proceed with the campaign creation because '.$check_time_adslot['from_to_time'].' for '.$check_time_adslot['broadcaster_name'].' isn`t available again';
                return $msg;
            }
        }
        try{
            Utilities::switch_db('api')->transaction(function () use($post_campaign_bank, $client_details, $broadcaster_details, $now) {
                $post_campaign_bank['store_campaign']->storeCampaign();
                $post_campaign_bank['store_mpo']->storeMpo();
                $post_campaign_bank['store_invoice']->storeInvoice();
                $post_campaign_bank['store_payment']->storePayment();
                $this->storeBroadcasterCampaignsInformation($post_campaign_bank,$client_details,$broadcaster_details,$now);
            });
        }catch (\Exception $exception){
            return 'error';
        }
        return 'success';
    }

    public function storeBroadcasterCampaignsInformation($post_campaign_bank, $client_details, $broadcaster_details, $now)
    {
        $campaign_details = new StoreCampaignDetails($post_campaign_bank['campaign_id'], $post_campaign_bank['user_id'], $this->broadcaster_id, null,
            $this->campaign_general_information, $client_details->id,$broadcaster_details->channels->first()->id, $now, $post_campaign_bank['adslot_ids'],null);
        $campaign_details->storeCampaingDetails();
        foreach ($post_campaign_bank['preselected_adslots'] as $preselected_adslot){
            $selected_adslot = new StoreSelectedAdslot($post_campaign_bank['campaign_id'], $preselected_adslot, $post_campaign_bank['user_id'], $now,
                null, $this->broadcaster_id);
            $selected_adslot->storeSelectedAdslot();
        }
        $store_payment_details = new StorePaymentDetails($post_campaign_bank['payment_id'], $this->broadcaster_id, null,$client_details->id,
            $this->campaign_general_information->campaign_budget,$post_campaign_bank['total_spent'], $now, null);
        $store_payment_details->storePaymentDetails();
        $store_invoice_details = new StoreInvoiceDetails($post_campaign_bank['invoice_id'], $post_campaign_bank['invoice_number'],
            $this->broadcaster_id,$this->agency_id, $post_campaign_bank['user_id'], $client_details->id, $post_campaign_bank['total_spent'], null);
        $store_invoice_details->storeInvoiceDetails();
        $store_mpo_details = new StoreMpoDetails($post_campaign_bank['mpo_id'], $this->broadcaster_id, null, null);
        $store_mpo_details->storeMpoDetails();
        $this->updateAdslotAndFilePositions($post_campaign_bank['preselected_adslots']);
        PreselectedAdslot::where('user_id', $post_campaign_bank['user_id'])->delete();
        Upload::where('user_id', $post_campaign_bank['user_id'])->delete();
    }

    public function agencyCampaignOnHold($campaign_id, $mpo_id, $payment_id, $invoice_id, $campaign_reference, $invoice_number, $now, $user_id, $client_details)
    {
        $preselected_adslots_object = new PreselectedAdslotService($user_id, null, $this->agency_id, null, null);
        $preselected_adslots = $preselected_adslots_object->getPreselectedSlots();
        $adslot_ids = $preselected_adslots_object->getAdslotIdFromPreselectedAdslot();
        $preselected_adslot_groups = $preselected_adslots_object->groupPreselectedAdslotByBoradscaster();
        $total_spent = $preselected_adslots_object->sumTotalPriceGroupedByBroadcaster();
        $wallet = new WelletService($this->agency_id);
        $wallet = $wallet->getCurrentBalance();
        if(!$wallet){
            return 'wallet_not_exist';
        }
        if((int)$wallet->current_balance < (int)$total_spent){
            return 'insufficient_fund';
        }
        foreach ($preselected_adslot_groups as $preselected_adslot_group){
            $check_time_adslots = $this->fetchTimeRemainders($user_id, $preselected_adslot_group->broadcaster_id);
            foreach ($check_time_adslots as $check_time_adslot){
                if($check_time_adslot['initial_time_left'] < $check_time_adslot['time_bought']){
                    $msg = 'You cannot proceed with the campaign creation because '.$check_time_adslot['from_to_time'].' for
                            '.$check_time_adslot['broadcaster_name'].' isn`t available again';
                    return $msg;
                }
            }
        }
        $campaign_data_bank = $this->collectInformationNeeded(null,null,null,null,$preselected_adslots,
            $campaign_id, $invoice_id, $mpo_id, $payment_id, $invoice_number, $adslot_ids, $total_spent, $user_id,$campaign_reference);
        try{
            $this->storeAgencyCampaignInformation($campaign_data_bank, $now, $preselected_adslot_groups, $client_details);
        }catch (\Exception $exception){
            return 'error';
        }
        return 'success';
    }

    public function storeAgencyCampaignInformation($campaign_data_bank, $now, $preselected_adslot_groups, $client_details)
    {
        Utilities::switch_db('api')->transaction(function () use($campaign_data_bank, $now, $preselected_adslot_groups, $client_details) {
            $store_campaign = new StoreCampaign($campaign_data_bank['campaign_id'], $now, $campaign_data_bank['campaign_reference']);
            $store_campaign->storeCampaign();
            $store_payments = new StorePayment($campaign_data_bank['payment_id'], $campaign_data_bank['campaign_id'], $campaign_data_bank['total_spent'],
                $now, $campaign_data_bank['campaign_reference'], $this->campaign_general_information->campaign_budget);
            $store_payments->storePayment();
            $store_mpo = new storeMpo($campaign_data_bank['mpo_id'], $campaign_data_bank['campaign_id'], $campaign_data_bank['invoice_number'],
                $campaign_data_bank['campaign_reference']);
            $store_mpo->storeMpo();
            $store_invoice = new StoreInvoice($campaign_data_bank['invoice_id'], $campaign_data_bank['campaign_id'], $campaign_data_bank['campaign_reference'],
                $campaign_data_bank['payment_id'], $campaign_data_bank['invoice_number']);
            $store_invoice->storeInvoice();
            foreach ($campaign_data_bank['preselected_adslots'] as $preselected_adslot){
                $selected_adslots = new StoreSelectedAdslot($campaign_data_bank['campaign_id'], $preselected_adslot, $campaign_data_bank['user_id'],
                    $now, $this->agency_id, null);
                $selected_adslots->storeSelectedAdslot();
            }
            foreach ($preselected_adslot_groups as $preselected_adslot_group){
                $store_campaign_details = new StoreCampaignDetails($campaign_data_bank['campaign_id'], $campaign_data_bank['user_id'], null, $this->agency_id,
                    $this->campaign_general_information, $client_details->id, null, $now, $campaign_data_bank['adslot_ids'], $preselected_adslot_group);
                $store_campaign_details->storeCampaingDetails();

                $store_payment_details = new StorePaymentDetails($campaign_data_bank['payment_id'], null, $this->agency_id, $client_details->id,
                    $this->campaign_general_information->campaign_budget, $campaign_data_bank['total_spent'], $now, $preselected_adslot_group);
                $store_payment_details->storePaymentDetails();

                $store_invoice_details = new StoreInvoiceDetails($campaign_data_bank['invoice_id'], $campaign_data_bank['invoice_number'], null,
                    $this->agency_id, $campaign_data_bank['user_id'], $client_details->id, $campaign_data_bank['total_spent'], $preselected_adslot_group);
                $store_invoice_details->storeInvoiceDetails();

                $store_mpo_details = new StoreMpoDetails($campaign_data_bank['mpo_id'], null, $this->agency_id, $preselected_adslot_group);
                $store_mpo_details->storeMpoDetails();
            }
            $this->updateAdslotAndFilePositions($campaign_data_bank['preselected_adslots']);
            PreselectedAdslot::where('user_id', $campaign_data_bank['user_id'])->delete();
            Upload::where('user_id', $campaign_data_bank['user_id'])->delete();
        });
    }

    public function adslotResultDetails()
    {
        $adslots_filter_result = new AdslotFilterResult($this->campaign_general_information,$this->campaign_general_information->start_date,
                                                        $this->campaign_general_information->end_date);
        $adslots_filter_result = $adslots_filter_result->adslotFilterResult();
        return $adslots_filter_result;
    }

    public function collectInformationNeeded($store_campaign,$store_mpo,$store_invoice,$store_payment,$preselected_adslots,$campaign_id,$invoice_id,$mpo_id,
                                             $payment_id,$invoice_number,$adslot_ids,$total_spent, $user_id, $campaign_reference)
    {
        return $post_campaign_bank = [
                    'store_campaign' => $store_campaign,
                    'store_mpo' => $store_mpo,
                    'store_invoice' => $store_invoice,
                    'store_payment' => $store_payment,
                    'preselected_adslots' => $preselected_adslots,
                    'campaign_id' => $campaign_id,
                    'invoice_id' => $invoice_id,
                    'mpo_id' => $mpo_id,
                    'payment_id' => $payment_id,
                    'invoice_number' => $invoice_number,
                    'adslot_ids' => $adslot_ids,
                    'total_spent' => $total_spent,
                    'user_id' => $user_id,
                    'campaign_reference' => $campaign_reference
                ];
    }

    public function updateAdslotAndFilePositions($preselected_adslots)
    {
        foreach ($preselected_adslots as $preselected_adslot){
            if(!empty($preselected_adslot->filePosition_id)){
                $adslot_file_position = new AdslotFilePositionService($preselected_adslot->adslot_id);
                $adslot_file_position->updateAdslotFilePosition();
            }
            $get_slots = Adslot::where('id', $preselected_adslot->adslot_id)->first();
            $time_difference = $get_slots->time_difference;
            $time_used = $get_slots->time_used;
            $time = $preselected_adslot->time;
            $new_time_used = $time_used + $time;
            if($time_difference === $new_time_used){
                $slot_status = 1;
            }else{
                $slot_status = 0;
            }
            $get_slots->time_used = $new_time_used;
            $get_slots->is_available = $slot_status;
            $get_slots->save();
        }
    }

    public function fetchTimeRemainders($user_id, $broadcaster_id)
    {
        $time_remainders = [];
        $preselected_adslots = new PreselectedAdslotService($user_id, null, null, null, null);
        $preselected_adslo_times = $preselected_adslots->groupSumDurationByAdslotId();
        $broadcaster_details_object = new CompanyDetails($broadcaster_id);
        foreach($preselected_adslo_times as $preselected_adslo_time){
            $check_adslot_space = Adslot::where('id', $preselected_adslo_time->adslot_id)->first();
            $time_left = (integer)$check_adslot_space->time_difference - (integer)$check_adslot_space->time_used;
            $broadcaster_details = $broadcaster_details_object->getCompanyDetails();
            $time_remainders[] = [
                'initial_time_left' => $time_left,
                'time_bought' => $preselected_adslo_time->summed_time,
                'adslot_id' => $preselected_adslo_time->adslot_id,
                'broadcaster_name' => $broadcaster_details->name,
                'from_to_time' => $check_adslot_space->from_to_time,
            ];
        }

        return $time_remainders;
    }

    public function updateBudget(Request $request)
    {
        $campaign_old_budget = Session::get('campaign_information')->campaign_budget;
        if((int)$campaign_old_budget < (int)$request->campaign_budget){
            Session::get('campaign_information')->campaign_budget = $request->campaign_budget;
            Session::flash('success', ClassMessages::CAMPAIGN_BUDGET_UPDATE);
            return back();
        }else{
            Session::get('error', ClassMessages::CAMPAIGN_BUDGET_ERROR);
            return redirect()->back();
        }
    }

}
