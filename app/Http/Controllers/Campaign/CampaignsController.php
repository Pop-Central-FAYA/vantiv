<?php

namespace Vanguard\Http\Controllers\Campaign;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Controller;
use Session;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\Campaigns\CampaignGeneralInformationRequest;
use Vanguard\Libraries\CampaignDate;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Payment;
use Vanguard\Models\PreselectedAdslot;
use Vanguard\Services\Adslot\AdslotFilterResult;
use Vanguard\Services\Adslot\PreselectedAdslotService;
use Vanguard\Services\Adslot\RatecardService;
use Vanguard\Services\Broadcaster\BroadcasterDetails;
use Vanguard\Services\Campaign\DeleteTemporaryUpload;
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
use Vanguard\Services\Client\AllClient;
use Vanguard\Services\Campaign\AllCampaign;
use Vanguard\Services\Client\ClientBrand;
use Vanguard\Services\Client\ClientDetails;
use Vanguard\Services\FilePosition\Fileposition;
use Vanguard\Services\Industry\IndustryAndSubindustry;
use Vanguard\Services\PreloadedData\PreloadedData;
use Vanguard\Services\Upload\MediaUploadProcessing;
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
        $campaigns = new AllCampaign($request, $this->utilities, $this->dataTables, $this->broadcaster_id, $this->agency_id, $dashboard = false);
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
        $clients = new AllClient($this->broadcaster_id, $this->agency_id);
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
                    ->with('broadcaster_details', $this->broadcaster_id ? $this->utilities->getBroadcasterDetails($this->broadcaster_id) : '')
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
        $broadcaster_details = $this->utilities->getBroadcasterDetails($broadcaster_id);
        $campaign_week = $this->utilities->getStartAndEndDateWithTheWeek($this->campaign_general_information->start_date,
                                                                        $this->campaign_general_information->end_date);
        $media_details = $this->utilities->getMediaUploadDetails($id, null);
        $preselected_adslots = $preselected_slots_service->getPreselectedSlots();
        $total_price = $preselected_slots_service->getSumPrice();
        $file_positions = $file_position_service->filePositionDetails();
        $rate_card_object = new RatecardService($this->campaign_general_information, $broadcaster_id,
                                                $start_date,$end_date,$broadcaster_details);
        $rate_cards = $rate_card_object->run();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($rate_cards['rate_cards']);
        $perPage = 100;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('/agency/campaigns/campaign/step4/'.$id.'/'.$this->broadcaster_id);

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
            ->with('campaign_general_information', $this->campaign_general_information);

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
        $adslot_results = new AdslotFilterResult($this->campaign_general_information, $this->broadcaster_id, $this->agency_id,
                                                $this->campaign_general_information->start_date,$this->campaign_general_information->end_date);
        $campaign_first_week = $this->campaign_date->getFirstWeek($this->campaign_general_information->start_date,
                                                                    $this->campaign_general_information->end_date);
        return view('campaigns.broadcaster_select')
                    ->with('adslot_search_results', $adslot_results->adslotFilterResult())
                    ->with('id', $id)
                    ->with('campaign_first_week', $campaign_first_week);
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
            return redirect()->back();
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

    public function postCampaignOnHold(Request $request, $user_id)
    {
        $preselected_adslot_object = new PreselectedAdslotService($user_id, $this->broadcaster_id, $this->agency_id,
                                                        null,null);
        $client_details_object = new ClientDetails(null, $user_id);
        $preselected_adslots = $preselected_adslot_object->getPreselectedSlots();
        $adslot_ids = $preselected_adslot_object->getAdslotIdFromPreselectedAdslot();
        $count_adslots = $preselected_adslot_object->countPreselectedAdslot();
        $client_details = $client_details_object->run();
        $total_spent = $preselected_adslot_object->sumTotalPriceGroupedByBroadcaster();
        $campaign_id = uniqid();
        $mpo_id = uniqid();
        $payment_id = uniqid();
        $invoice_id = uniqid();
        $campaign_reference = Utilities::generateReference();
        $invoice_number = Utilities::generateReference();
        $now = strtotime(Carbon::now('Africa/Lagos'));
        $broadcaster_details = new BroadcasterDetails($this->broadcaster_id);
        $broadcaster_details = $broadcaster_details->getBroadcasterDetails();

        $store_campaign = new StoreCampaign($campaign_id, $now, $campaign_reference);
        $store_mpo = new storeMpo($mpo_id, $campaign_id, $invoice_number, $campaign_reference);
        $store_invoice = new StoreInvoice($invoice_id, $campaign_id, $campaign_reference, $payment_id, $invoice_number);
        $store_payment = new StorePayment($payment_id, $campaign_id, $request->total, $now, $campaign_reference, $this->campaign_general_information->budget);

        if($this->broadcaster_id){
            $post_campaign_bank = $this->collectInformationNeeded($store_campaign, $store_mpo, $store_invoice, $store_payment, $preselected_adslots,
                                                                $campaign_id, $invoice_id, $mpo_id, $payment_id, $invoice_number, $adslot_ids, $total_spent);
            try{
                $this->storeBroadcasterCampaignsInformation($post_campaign_bank, $user_id, $client_details, $broadcaster_details, $now);
            }catch (\Exception $exception){
                dd($exception);
            }

        }else{

        }
    }

    public function storeBroadcasterCampaignsInformation($post_campaign_bank, $user_id, $client_details, $broadcaster_details, $now)
    {
        \DB::transaction(function () use($post_campaign_bank, $user_id, $client_details, $broadcaster_details, $now) {
            $post_campaign_bank['store_campaign']->storeCampaign();
            $post_campaign_bank['store_mpo']->storeMpo();
            $post_campaign_bank['store_invoice']->storeInvoice();
            $post_campaign_bank['store_payment']->storePayment();
            $campaign_details = new StoreCampaignDetails($post_campaign_bank['campaign_id'], $user_id, $this->broadcaster_id, $this->agency_id,
                $this->campaign_general_information, $client_details->id,
                $broadcaster_details, $now, $post_campaign_bank['adslot_ids']);
            $campaign_details->storeCampaingDetails();
            foreach ($post_campaign_bank['preselected_adslots'] as $preselected_adslot){
                $selected_adslot = new StoreSelectedAdslot($post_campaign_bank['campaign_id'], $preselected_adslot, $user_id, $now,
                    $this->agency_id, $this->broadcaster_id);
                $selected_adslot->storeSelectedAdslot();
            }
            $store_payment_details = new StorePaymentDetails($post_campaign_bank['payment_id'], $this->broadcaster_id, $this->agency_id,$client_details->id,
                $this->campaign_general_information->campaign_budget,$post_campaign_bank['total_spent'], $now);
            $store_payment_details->storePaymentDetails();
            $store_invoice_details = new StoreInvoiceDetails($post_campaign_bank['invoice_id'], $post_campaign_bank['invoice_number'],
                $this->broadcaster_id,$this->agency_id, $user_id, $client_details->id, $post_campaign_bank['total_spent']);
            $store_invoice_details->storeInvoiceDetails();
            $store_mpo_details = new StoreMpoDetails($post_campaign_bank['mpo_id'], $this->broadcaster_id, $this->agency_id);
            $store_mpo_details->storeMpoDetails();
        });
    }

    public function storeAgencyCampaignInformation()
    {

    }

    public function adslotResultDetails()
    {
        $adslots_filter_result = new AdslotFilterResult($this->campaign_general_information, $this->broadcaster_id,
            $this->agency_id, $this->campaign_general_information->start_date, $this->campaign_general_information->end_date);
        $adslots_filter_result = $adslots_filter_result->adslotFilterResult();
        return $adslots_filter_result;
    }

    public function collectInformationNeeded($store_campaign,$store_mpo,$store_invoice,$store_payment,$preselected_adslots,$campaign_id,$invoice_id,$mpo_id,
                                             $payment_id,$invoice_number,$adslot_ids,$total_spent)
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
                    'total_spent' => $total_spent
                ];
    }

}
