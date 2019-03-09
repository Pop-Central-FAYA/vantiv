<?php

namespace Vanguard\Http\Controllers\Agency;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\CampaignInformationUpdateRequest;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\CampaignDate;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\PreselectedAdslot;
use Vanguard\Models\SelectedAdslot;
use Vanguard\Models\Upload;
use Vanguard\Services\Campaign\MediaMix;
use Vanguard\Services\Campaign\CampaignBudgetGraph;
use Session;
use Vanguard\Services\Compliance\ComplianceGraph;

class CampaignsController extends Controller
{
    private $campaign_success_message = 'Campaign created successfully, please review and submit';
    private $campaign_dates, $utilities;

    public function __construct(CampaignDate $campaignDate, Utilities $utilities)
    {
        $this->campaign_dates = $campaignDate;
        $this->utilities = $utilities;
    }

    public function getStep1()
    {

        if(Session::get('first_step')){
            $first_step = Session::get('first_step');
            $brands = Utilities::getBrandsForWalkins($first_step->client);
        }
        $agency_id = Session::get('agency_id');
        $walkins = Utilities::switch_db('api')->select("SELECT * from walkIns where agency_id = '$agency_id'");
        $preloaded_data = Utilities::getPreloadedData();
        $channels = Utilities::switch_db('api')->select("SELECT * from campaignChannels where channel = 'TV'");

        Api::validateCampaign();

        return view('agency.campaigns.create1')->with('industries', $preloaded_data['industries'])
            ->with('channels', $channels)
            ->with('regions', $preloaded_data['regions'])
            ->with('day_parts', $preloaded_data['day_parts'])
            ->with('targets', $preloaded_data['target_audience'])
            ->with('clients', $walkins)
            ->with('first_step', Session::get('first_step') ? $first_step : '')
            ->with('brands', Session::get('first_step') ? $brands : '')
            ->with('sub_industries', $preloaded_data['subindustries']);
    }

    public function postStep1(Request $request)
    {
        $this->validate($request, [
            'client' => 'required',
            'campaign_name' => 'required',
            'brand' => 'required',
            'product' => 'required',
            'channel' => 'required_without_all',
            'min_age' => 'required',
            'max_age' => 'required',
            'target_audience' => 'required_without_all',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'industry' => 'required',
            'dayparts' => 'required_without_all',
            'region' => 'required_without_all',
        ]);

        $agency_id = Session::get('agency_id');

        $result = Utilities::sessionizedRequest($request, null, $agency_id);
        if($result === 'error_negative'){
            Session::flash('error', 'The minimum or maximum age cannot have a negative value');
            return back();
        }elseif ($result === 'error_age'){
            Session::flash('error', 'The minimum age cannot be greater than the maximum age');
            return back();
        }elseif ($result === 'error_date'){
            Session::flash('error', 'Start Date cannot be greater than End Date');
            return redirect()->back();
        }

        return redirect()->route('agency_campaign.step2', ['id' => $result])
            ->with('step_1', Session::get('first_step'))
            ->with('id', $result);
    }

    public function getStep2($id)
    {
        $step1 = Session::get('first_step');

        $result_check = Utilities::checkRequestSession($step1);
        if($result_check === 'data_lost'){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }


        $agency_id = Session::get('agency_id');

        $ads_broad = Utilities::adslotFilter($step1, null, $agency_id);

        return view('agency.campaigns.create2')->with('adslots', $ads_broad)
            ->with('id', $id);
    }

    public function getStep3($id)
    {
        $agency_id = Session::get('agency_id');
        Upload::where([
            ['user_id', $id],
            ['time', 0]
        ])->delete();

        $step1 = Session::get('first_step');

        $result_check = Utilities::checkRequestSession($step1);
        if($result_check === 'data_lost'){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }


        $ads_broad = Utilities::adslotFilter($step1, null, $agency_id);

        if(count($ads_broad) === 0){
            Session::flash('error', 'No adslots matches your filter criteria, please go back and re-adjust your requirements');
            return redirect()->back();
        }

        return view('agency.campaigns.create3')->with('id', $id)->with('first_step', $step1);
    }

    public function postStep3($id)
    {
        $upload = Utilities::uploadMedia();
        if($upload === 'error'){
            return response()->json(['error' => 'error']);
        }elseif($upload === 'error_number'){
            return response()->json(['error_number' => 'error_number']);
        }elseif($upload === 'error_check_image'){
            return response()->json(['error_check_image' => 'error_check_image']);
        }elseif($upload === 'success'){
            return response()->json(['success' => 'success']);
        }
    }

    public function getStep3_1($id)
    {
        return view('agency.campaigns.create3_1')->with('id', $id);
    }

    public function postStep3_1($id)
    {
        $first_step = Session::get('first_step');
        foreach($first_step->channel as $channel){
            $channel_name = Utilities::switch_db('api')->select("SELECT * FROM campaignChannels where id = '$channel'");
            $get_uploaded_files = Upload::where([
                ['user_id', $id],
                ['channel', $channel]
            ])->get();
            $count_files = count($get_uploaded_files);
            if($count_files === 0){
                $msg = 'You have not uploaded any file(s) for '.$channel_name[0]->channel;
                Session::flash('error', $msg);
                return redirect()->back();
            }else{
                $remaining_file = 4 - $count_files;
                for ($i = 0; $i < $remaining_file; $i++){
                    Upload::create([
                        'user_id' => $id,
                        'time' => 00,
                        'channel' => $channel
                    ]);
                }

            }
        }

        return redirect()->route('agency_campaign.step3_2', ['id' => $id]);

    }

    public function getStep3_2($id)
    {
        $agency_id = Session::get('agency_id');
        $first_step = Session::get('first_step');
        $ads_broad = Utilities::adslotFilter($first_step, null, $agency_id );
        $campaign_dates_for_first_week = $this->campaign_dates->getFirstWeek($first_step->start_date, $first_step->end_date);
        return view('agency.campaigns.create3_2')->with('adslot_search_results', $ads_broad)->with('id', $id)->with('campaign_dates_for_first_week', $campaign_dates_for_first_week);
    }

    public function getStep4($id, $broadcaster, $start_date, $end_date)
    {
        ini_set('memory_limit','512M');
        $step1 = Session::get('first_step');
        $agency_id = Session::get('agency_id');
        $result_check = Utilities::checkRequestSession($step1);
        if($result_check === 'data_lost'){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }

        $time_breaks = Utilities::switch_db('api')->select("SELECT a.from_to_time, a.id as adslot_id, r.day as day_id from adslots as a 
                                                              INNER JOIN rateCards as r ON r.id = a.rate_card
                                                              where a.broadcaster = '$broadcaster'");

        $ratecards = $this->utilities->getRateCards($step1, $broadcaster, $start_date, $end_date);

        $r = $ratecards['rate_card'];

        $adslots = $ratecards['adslot'];

        $ads_broad = Utilities::adslotFilter($step1, null, $agency_id);

        $campaign_dates_by_week_with_start_end_date = $this->utilities->getStartAndEndDateWithTheWeek($step1->start_date, $step1->end_date);

        $first_week = $this->campaign_dates->getFirstWeek($step1->start_date, $step1->end_date);
        $start_and_end_date_in_first_week = $this->campaign_dates->getStartAndEndDateForFirstWeek($first_week);

        $time = [15, 30, 45, 60];

        $uploads_data = Upload::where('user_id', $id)->get();
        $preselected_adslots = PreselectedAdslot::where('user_id', $id)->get();
        $total_price_preselected_adslot = Utilities::switch_db('api')->select("SELECT SUM(total_price) as total from preselected_adslots where user_id = '$id'");
        $positions = Utilities::switch_db('api')->select("SELECT * from filePositions where broadcaster_id = '$broadcaster'");

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($r);
        $perPage = 100;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('/agency/campaigns/campaign/step4/'.$id.'/'.$broadcaster);

        return view('agency.campaigns.create4')->with('ratecards', $entries)
                                                    ->with('total_amount', $total_price_preselected_adslot)
                                                    ->with('ads_broads', $ads_broad)
                                                    ->with('preselected_adslots', $preselected_adslots)
                                                    ->with('uploaded_data', $uploads_data)
                                                    ->with('times', $time)
                                                    ->with('id', $id)
                                                    ->with('broadcaster', $broadcaster)
                                                    ->with('positions', $positions)
                                                    ->with('adslots', $adslots)
                                                    ->with('campaign_dates_by_week', $campaign_dates_by_week_with_start_end_date)
                                                    ->with('start_and_end_date_in_first_week', $start_and_end_date_in_first_week)
                                                    ->with('time_breaks', $time_breaks);
    }

    public function postPreselectedAdslot(Request $request)
    {
        $first = Session::get('first_step');
        $agency_id = Session::get('agency_id');
        $insert = Utilities::storeCart($request, $first, $agency_id, null);
        if($insert === 'file_error'){
            return response()->json(['file_error' => 'file_error']);
        }elseif($insert === 'budget_exceed_error'){
            return response()->json(['budget_exceed_error' => 'budget_exceed_error']);
        }elseif($insert === 'error'){
            return response()->json(['error' => 'error']);
        }elseif(!$insert){
            return response()->json(['failure' => 'failure']);
        }else{
            return response()->json(['success' => 'success']);
        }
    }

    public function checkout($id)
    {
        $check_preselected_adslot = PreselectedAdslot::where('user_id', $id)->get();
        if(count($check_preselected_adslot) === 0){
            Session::flash('error', 'Your cart is empty...');
            return redirect()->back();
        }
        $agency_id = Session::get('agency_id');
        $first = Session::get('first_step');
        $checkout = Utilities::getCheckout($id, $first, $agency_id, null);
        $wallet_balance = Utilities::switch_db('api')->select("SELECT current_balance FROM wallets where user_id = '$agency_id'");

        //add pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($checkout['preselected_adslot_arrays']);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath($id);

        return view('agency.campaigns.checkout')->with('first_session', $first)
            ->with('calc', $checkout['calc'])
            ->with('day_part', $checkout['day_parts'])
            ->with('region', $checkout['regions'])
            ->with('target', $checkout['targets'])
            ->with('preselected_adslot_arrays', $entries)
            ->with('brand', $checkout['brands'])
            ->with('id', $id)
            ->with('wallet_balance', $wallet_balance)
            ->with('agency_id', $agency_id);
    }

    public function removeCart($id)
    {
        PreselectedAdslot::where('id', $id)->delete();
        Session::flash('success', 'Item deleted from cart successfully');
        return redirect()->back();
    }

    Public function postCampaign(Request $request, $id)
    {
        $agency_id = Session::get('agency_id');
        //check if he agency's wallet can cater for the campaign
        $wallet_balance = Utilities::switch_db('api')->select("SELECT current_balance FROM wallets where user_id = '$agency_id'");
        if(!$wallet_balance){
            Session::flash('error', 'Please Fund your wallet');
            return redirect()->back();
        }

        if((int)$wallet_balance[0]->current_balance < (int)$request->total){
            Session::flash('error', 'Insufficient fund, please fund your wallet to complete campaign creation');
            return redirect()->back();
        }

        $first = Session::get('first_step');
        $api_db = Utilities::switch_db('api');
        $local_db = Utilities::switch_db('local');
        $preselected_adslots = PreselectedAdslot::where([
            ['user_id', $id],
            ['agency_id', $agency_id]
        ])->get();
        $ads = [];

        $user_id = $id;

        foreach ($preselected_adslots as $preselected_adslot) {
            $ads[] = $preselected_adslot->adslot_id;
        }

        //come back here
        $group_data = $api_db->select("SELECT SUM(total_price) AS total, COUNT(id) AS total_slot, broadcaster_id FROM preselected_adslots
                                          WHERE user_id = '$id' AND agency_id = '$agency_id' GROUP BY broadcaster_id");

        $request->all();
        $saveFiles = [];
        $payments = [];
        $paymentDetails = [];
        $campaigns = [];
        $campaignDetails = [];
        $invoice = [];
        $invoiceDetails = [];
        $mpo = [];
        $mpoDetails = [];
        $campaign_id = uniqid();
        $pay_id = uniqid();
        $invoice_id = uniqid();
        $mpo_id = uniqid();
        $campaign_reference = Utilities::generateReference();
        $invoice_number = Utilities::generateReference();
        $walkin_id = $api_db->select("SELECT id from walkIns where user_id = '$id'");
        $now = strtotime(Carbon::now('Africa/Lagos'));

        $campaigns[] = Utilities::campaignInformation($campaign_id, $campaign_reference, $now);

        foreach ($group_data as $group_datum){
            $campaignDetails[] = Utilities::campaignDetailsInformations($first, $campaign_id, $id, $now, $ads, $group_datum, $agency_id, $walkin_id,
                                                                null, null, $preselected_adslots);
            $check_time_adslots = Utilities::fetchTimeInCart($id, $group_datum->broadcaster_id);
            foreach ($check_time_adslots as $check_time_adslot){
                if($check_time_adslot['initial_time_left'] < $check_time_adslot['time_bought']){
                    $msg = 'You cannot proceed with the campaign creation because '.$check_time_adslot['from_to_time'].' for
                            '.$check_time_adslot['broadcaster_name'].' isn`t available again';
                    \Session::flash('info', $msg);
                    return back();
                }
            }
        }

        $save_campaign = $this->storeCampaignsPaymentsFilesMposPayments($campaigns, $campaignDetails, $campaign_id, $preselected_adslots, $id, $now, $agency_id, $saveFiles,
            $payments, $pay_id, $request, $first, $group_data, $walkin_id, $paymentDetails, $invoice_id,
            $invoice_number, $invoice, $invoiceDetails, $mpo, $mpo_id, $mpoDetails, $user_id);

        if($save_campaign === 'error'){
            Session::flash('error', 'There was problem creating campaign');
            return redirect()->back();
        }

        Session::forget('first_step');
        Session::flash('success', $this->campaign_success_message);
        return redirect()->route('agency.campaigns_onhold');

    }

    public function getDetails($id)
    {
        $agency_id = \Session::get('agency_id');
        $campaign_details = Utilities::campaignDetails($id, null, $agency_id);
        $user_id = $campaign_details['campaign_det']['company_user_id'];
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' and user_id = '$user_id' GROUP BY campaign_id");
        $all_clients = Utilities::switch_db('api')->select("SELECT * FROM walkIns where agency_id = '$agency_id'");
        return view('agency.campaigns.campaign_details', compact('campaign_details', 'all_campaigns', 'all_clients'));
    }

    public function filterByUser($user_id)
    {
        $agency_id = \Session::get('agency_id');
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where agency = '$agency_id' and user_id = '$user_id' GROUP BY campaign_id");

        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where user_id = '$user_id')");
        return (['campaign' => $all_campaigns, 'channel' => $media_chanel]);
    }

    public function filterByCampaignId($campaign_id)
    {
        $agency_id = Session::get('agency_id');
        $summary = Utilities::campaignDetails($campaign_id, null, $agency_id);
        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where campaign_id = '$campaign_id')");
        return response()->json(['media_channel' => $media_chanel, 'summary' => $summary]);
    }

    public function mpoDetails($id)
    {
        $agency_id = Session::get('agency_id');
        $mpo_details = Utilities::getMpoDetails($id, $agency_id);

        return view('agency.mpo.mpo')->with('mpo_details', $mpo_details);
    }

    public function getMediaChannel($campaign_id)
    {
        $channel = request()->channel;
        $broadcaster_retain = request()->media_channel;
        if($channel){
            $all_company_channels_service = new MediaMix($campaign_id, $channel, $broadcaster_retain);

            return response()->json(['all_channel' => $all_company_channels_service->getAllCompanyWithChannelInCampaign(),
                                    'media_mix' => $all_company_channels_service->getMediaMixData(),
                                    'retained_channel' => !empty($broadcaster_retain) ? $all_company_channels_service->getRetainedCompany() : null]);
        }else{
            return null;
        }
    }

    public function campaignBudgetGraph()
    {
        $campaign_id = request()->campaign_id;
        $media_publishers = request()->channel;
        if($media_publishers){
            $compliance_graph_service = new CampaignBudgetGraph($campaign_id, $media_publishers);
            return $compliance_graph_service->getCampaignBudgetData();
        }else{
            return null;
        }

    }

    public function complianceGraph()
    {
        $campaign_id = request()->campaign_id;
        $start_date = date('Y-m-d', strtotime(request()->start_date));
        $stop_date = date('Y-m-d', strtotime(request()->stop_date));
        $publisher_id = request()->media_channel;

        $compliance_graph_service = new ComplianceGraph($campaign_id, $start_date, $stop_date, $publisher_id);

        return response()->json(['date' => $compliance_graph_service->getComplianceDates(),
                                'data' => $compliance_graph_service->formatDataForGraphCompatibility()]);
    }

    public function updateBudget(Request $request)
    {
        Session::get('first_step')->campaign_budget = $request->campaign_budget;
        Session::flash('success', 'Campaign Budget Updated');
        return back();
    }

    public function storeCampaignsPaymentsFilesMposPayments($campaigns, $campaignDetails, $campaign_id, $preselected_adslots, $id, $now, $agency_id, $saveFiles,
                                                            $payments, $pay_id, $request, $first, $group_data, $walkin_id, $paymentDetails, $invoice_id,
                                                            $invoice_number, $invoice, $invoiceDetails, $mpo, $mpo_id, $mpoDetails, $user_id)
    {
        try {
            Utilities::switch_db('api')->transaction(function () use($campaigns, $campaignDetails, $campaign_id, $preselected_adslots, $id, $now, $agency_id, $saveFiles,
                $payments, $pay_id, $request, $first, $group_data, $walkin_id, $paymentDetails, $invoice_id,
                $invoice_number, $invoice, $invoiceDetails, $mpo, $mpo_id, $mpoDetails, $user_id) {
                Utilities::switch_db('api')->table('campaigns')->insert($campaigns);
                Utilities::switch_db('api')->table('campaignDetails')->insert($campaignDetails);
                $campaign_details = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE id='$campaign_id'");
                foreach($preselected_adslots as $preselected_adslot)
                {
                    SelectedAdslot::create(Utilities::campaignFileInformation($campaign_details, $preselected_adslot, $id, $now, $agency_id, null));
                }
                $payments[] = Utilities::campaignPaymentInformation($pay_id, $campaign_details, $request, $now, $first);
                foreach ($group_data as $group_datum){
                    $paymentDetails[] = Utilities::campaignPaymentDetailsInformation($pay_id, $request, $group_datum, $walkin_id, $now,
                        $agency_id, $first, null, null);
                }
                Utilities::switch_db('api')->table('payments')->insert($payments);
                Utilities::switch_db('api')->table('paymentDetails')->insert($paymentDetails);
                $payment_id = Utilities::switch_db('api')->select("SELECT id from payments WHERE id='$pay_id'");
                $invoice[] = Utilities::campaignInvoiceInformation($invoice_id, $campaign_details, $invoice_number, $payment_id);
                foreach ($group_data as $group_datum) {
                    $invoiceDetails[] = Utilities::campaignInvoiceDetailsInformation($invoice_id, $id, $invoice_number, $group_datum, $walkin_id,
                        $agency_id, null, null);
                }
                $mpo[] = Utilities::campaignMpoInformation($mpo_id, $campaign_details, $invoice_number);
                foreach ($group_data as $group_datum) {
                    $mpoDetails[] = Utilities::campaignMpoDetailsInformation($mpo_id, $agency_id, $group_datum, null);
                }
                Utilities::switch_db('api')->table('invoices')->insert($invoice);
                Utilities::switch_db('api')->table('invoiceDetails')->insert($invoiceDetails);
                Utilities::switch_db('api')->table('mpos')->insert($mpo);
                Utilities::switch_db('api')->table('mpoDetails')->insert($mpoDetails);
                foreach ($preselected_adslots as $preselected_adslot) {
                    if (!empty($preselected_adslot->filePosition_id)) {
                        Utilities::switch_db('api')->update("UPDATE adslot_filePositions set select_status = 1 WHERE adslot_id = '$preselected_adslot->adslot_id' ");
                    }

                    $get_slots = Utilities::switch_db('api')->select("SELECT * from adslots WHERE id = '$preselected_adslot->adslot_id'");
                    $slots_id = $get_slots[0]->id;
                    $time_difference = (integer)$get_slots[0]->time_difference;
                    $time_used = (integer)$get_slots[0]->time_used;
                    $time = (integer)$preselected_adslot->time;
                    $new_time_used = $time_used + $time;
                    if ($time_difference === $new_time_used) {
                        $slot_status = 1;
                    } else {
                        $slot_status = 0;
                    }
                    Utilities::switch_db('api')->update("UPDATE adslots SET time_used = '$new_time_used', is_available = '$slot_status' WHERE id = '$slots_id'");
                    PreselectedAdslot::where([
                        ['user_id', $user_id],
                        ['agency_id', $agency_id]
                    ])->delete();
                    Upload::where('user_id', $user_id)->delete();
                    $description = 'Campaign '.$first->campaign_name.' created successfully by '.Session::get('agency_id');
                    Api::saveActivity($agency_id, $description);
                }
            });
        }catch (\Exception $e){
            return 'error';
        }
    }

    public function campaignsOnHold()
    {
        $agency_id = Session::get('agency_id');
        $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, c_d.start_date, c_d.time_created, c_d.product,
                                                                        c_d.name, c_d.campaign_id, p.total, p.id as payment_id, b.name as brand_name, c_d.user_id as user_id,
                                                                        CONCAT(u.firstname,' ', u.lastname) as full_name, u.phone_number, u.email as email,
                                                                      c.campaign_reference from campaignDetails as c_d LEFT JOIN payments as p ON p.campaign_id = c_d.campaign_id
                                                                       LEFT JOIN campaigns as c ON c.id = c_d.campaign_id LEFT JOIN brands as b ON b.id = c_d.brand
                                                                       INNER JOIN users as u ON u.id = c_d.user_id
                                                                       where  c_d.agency = '$agency_id'
                                                                       and c_d.status = 'on_hold' and c_d.adslots  > 0 GROUP BY c_d.campaign_id ORDER BY c_d.time_created DESC");

        $campaigns = Utilities::getCampaignDatatablesforCampaignOnHold($all_campaigns);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($campaigns);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $campaigns = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $campaigns->setPath('data');
        return view('agency.campaigns.campaign_onhold', compact('campaigns'));
    }

    public function submitCampaignForProcessing($campaign_id)
    {
        $agency_id = Session::get('agency_id');
        $check_campaign_start_date = Utilities::checkIfCampaignStartDateHasReached($campaign_id, null, $agency_id);
        if($check_campaign_start_date == 'error'){
            Session::flash('error', 'Campaign cant be submitted because the start date has exceeded the current date');
            return redirect()->back();
        }
        $api_db = Utilities::switch_db('api');
        $single_campaign = $api_db->select("SELECT c_d.campaign_id, p.id as payment_id, p.total as total, i.id as invoice_id from campaignDetails as c_d
                                            INNER JOIN payments as p ON p.campaign_id = c_d.campaign_id
                                            INNER JOIN invoices as i ON i.campaign_id = c_d.campaign_id
                                            where  c_d.agency = '$agency_id'
                                            and c_d.campaign_id = '$campaign_id' and c_d.adslots  > 0 GROUP BY broadcaster ORDER BY c_d.time_created DESC");

        $wallet = Utilities::switch_db('reports')->select("SELECT * FROM wallets WHERE user_id = '$agency_id'");
        $total = $single_campaign[0]->total;
        $current_balance = $wallet[0]->current_balance;
        if ((int)$wallet[0]->current_balance < $total) {
            Session::flash('error', 'Insufficient balance in your wallet');
            return redirect()->back();
        }
        $campaign_id = $single_campaign[0]->campaign_id;
        $payment_id = $single_campaign[0]->payment_id;
        $new_balance = $current_balance - $total;
        $invoice_id = $single_campaign[0]->invoice_id;

        try {
            $api_db->transaction(function () use ($api_db, $campaign_id, $payment_id, $total, $agency_id, $current_balance, $new_balance, $invoice_id) {
                $api_db->update("UPDATE campaignDetails set status = 'pending' WHERE campaign_id = '$campaign_id'");
                $api_db->update("UPDATE paymentDetails set payment_method = 'WALLET_PAYMENT', payment_status = 1 where payment_id = '$payment_id'");
                $api_db->update("UPDATE invoiceDetails set status = 1 WHERE invoice_id = '$invoice_id'");
                $api_db->table('transactions')->insert([
                    'id' => uniqid(),
                    'amount' => $total,
                    'user_id' => $agency_id,
                    'reference' => $invoice_id,
                    'ip_address' => request()->ip(),
                    'type' => 'DEBIT WALLET',
                    'message' => 'Debit successful'
                ]);

                $api_db->table('walletHistories')->insert(Utilities::transactionHistory($agency_id, $total, $new_balance, $current_balance));
                $api_db->select("UPDATE wallets SET current_balance = '$new_balance', prev_balance = '$current_balance'
                                                                        WHERE user_id = '$agency_id'");
            });
        }catch (\Exception $e) {
            Session::flash('error', 'Error occurred while performing your request');
            return redirect()->back();
        }

        Session::flash('success', 'Campaign submitted successfully');
        return redirect()->route('dashboard');

    }

    public function updateAgencyCampaignInformation(CampaignInformationUpdateRequest $request, $campaign_id)
    {
        $agency_id = Session::get('agency_id');
        $check_campaign_start_date = Utilities::checkIfCampaignStartDateHasReached($campaign_id, null, $agency_id);
        if($check_campaign_start_date == 'error'){
            Session::flash('error', 'Campaign cant be submitted because the start date has exceeded the current date');
            return redirect()->back();
        }
        Utilities::switch_db('api')->update("UPDATE campaignDetails set name = '$request->name', product = '$request->product' WHERE campaign_id = '$campaign_id'");

        Session::flash('success', 'Campaign Information updated');
        return redirect()->back();
    }

}
