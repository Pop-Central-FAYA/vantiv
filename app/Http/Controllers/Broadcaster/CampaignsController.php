<?php

namespace Vanguard\Http\Controllers\Broadcaster;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Cloudinary;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Http\Requests\CampaignInformationUpdateRequest;
use Vanguard\Libraries\AmazonS3;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Maths;
use Vanguard\Libraries\Paystack;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\SelectedAdslot;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Session;
use Vanguard\Http\Controllers\Controller;


class CampaignsController extends Controller
{
    private $campaign_success_message = 'Campaign created successfully, please review and submit';

    public function setup()
    {
        return view('campaign.new-campaign');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('broadcaster_module.campaigns.index');
    }

    public function getAllData(DataTables $dataTables, Request $request)
    {
        //campaigns
        $broadcaster_id = Session::get('broadcaster_id');
        $today_date = date("Y-m-d");

        if($request->has('start_date') && $request->has('stop_date')) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, p.total, b.name as brand_name,
                                                                      c.campaign_reference from campaignDetails as c_d LEFT JOIN payments as p ON p.campaign_id = c_d.campaign_id LEFT JOIN campaigns as c ON c.id = c_d.campaign_id
                                                                      LEFT JOIN brands as b ON b.id = c_d.brand where  c_d.broadcaster = '$broadcaster_id' and c_d.start_date <= '$today_date' and c_d.stop_date > '$today_date'
                                                                      and c_d.status = 'active' and c_d.adslots  > 0 ORDER BY c_d.time_created DESC");
        }else {
            $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, p.total, b.name as brand_name,
                                                                      c.campaign_reference from campaignDetails as c_d LEFT JOIN payments as p ON p.campaign_id = c_d.campaign_id
                                                                       LEFT JOIN campaigns as c ON c.id = c_d.campaign_id LEFT JOIN brands as b ON b.id = c_d.brand where  c_d.broadcaster = '$broadcaster_id'
                                                                       and c_d.status = 'active' and c_d.adslots  > 0 ORDER BY c_d.time_created DESC");
        }

        $campaigns = Utilities::getCampaignDatatables($all_campaigns);

        return $dataTables->collection($campaigns)
            ->addColumn('name', function ($campaigns) {
                return '<a href="'.route('broadcaster.campaign.details', ['id' => $campaigns['campaign_id']]).'">'.$campaigns['name'].'</a>';
            })
            ->editColumn('status', function ($campaigns){
                if($campaigns['status'] === "on_hold"){
                    return '<span class="span_state status_on_hold">On Hold</span>';
                }elseif ($campaigns['status'] === "pending"){
                    return '<span class="span_state status_pending">Pending</span>';
                }elseif ($campaigns['status'] === 'expired'){
                    return '<span class="span_state status_danger">Finished</span>';
                }elseif($campaigns['status'] === 'active') {
                    return '<span class="span_state status_success">Active</span>';
                }else {
                    return '<span class="span_state status_danger">File Errors</span>';
                }
            })
            ->rawColumns(['status' => 'status', 'name' => 'name'])
            ->addIndexColumn()
            ->make(true);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        if(Session::get('first_step')){
            $first_step = Session::get('first_step');
            $brands = Utilities::getBrandsForWalkins($first_step->client);
        }
        $walkins = Utilities::switch_db('api')->select("SELECT * from walkIns where broadcaster_id = '$broadcaster_id'");
        $preloaded_data = Utilities::getPreloadedData();

        Api::validateCampaign();

        return view('broadcaster_module.campaigns.create_step1')
            ->with('industries', $preloaded_data['industries'])
            ->with('regions', $preloaded_data['regions'])
            ->with('day_parts', $preloaded_data['day_parts'])
            ->with('targets', $preloaded_data['target_audience'])
            ->with('clients', $walkins)
            ->with('first_step', Session::get('first_step') ? $first_step : '')
            ->with('brands', Session::get('first_step') ? $brands : '')
            ->with('sub_industries', $preloaded_data['subindustries']);
    }

    public function getIndustrySubIndustry()
    {
        $brand_id = request()->brand;
        $brand = Utilities::switch_db('api')->select("SELECT * from brands where id = '$brand_id'");
        $industry_id = $brand[0]->industry_code;
        $sub_industry_id = $brand[0]->sub_industry_code;
        $industry = Utilities::switch_db('api')->select("SELECT * from sectors where sector_code = '$industry_id'");
        $sub_industry = Utilities::switch_db('api')->select("SELECT * from subSectors where sub_sector_code = '$sub_industry_id'");
        if($industry && $sub_industry){
            return response()->json(['industry' => $industry, 'sub_industry' => $sub_industry]);
        }else{
            return response()->json(['error' => 'error']);
        }
    }

    public function postStep1(Request $request)
    {
        $this->validate($request, [
            'client' => 'required',
            'campaign_name' => 'required',
            'brand' => 'required',
            'product' => 'required',
            'min_age' => 'required',
            'max_age' => 'required',
            'target_audience' => 'required_without_all',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'industry' => 'required',
            'dayparts' => 'required_without_all',
            'region' => 'required_without_all',
        ]);

        $broadcaster_id = Session::get('broadcaster_id');

        $result = Utilities::sessionizedRequest($request, $broadcaster_id, null);
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
        return redirect()->route('campaign.create2', ['id' => $result])
            ->with('step_1', Session::get('first_step'))
            ->with('id', $result);
    }

    public function createStep2($id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $step1 = Session::get('first_step');

        $result = Utilities::checkRequestSession($step1);
        if($result === 'data_lost'){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }

        $adslots = Utilities::adslotFilter($step1, $broadcaster_id, null);

        return view('broadcaster_module.campaigns.create_step2')->with('adslots', $adslots)
            ->with('id', $id);
    }

    public function createStep3($id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $delete_uploads_without_files = \DB::delete("DELETE from uploads where user_id = '$id' AND time = 0");

        $first_step = Session::get('first_step');

        $result = Utilities::checkRequestSession($first_step);
        if($result === 'data_lost'){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }

        $adslots = Utilities::adslotFilter($first_step, $broadcaster_id, null);

        if(count($adslots) === 0){
            Session::flash('error', 'No adslots matches your filter criteria, please go back and re-adjust your requirements');
            return redirect()->back();
        }

        $broadcaster_details = Utilities::getBroadcasterDetails($broadcaster_id);
        return view('broadcaster_module.campaigns.create_step3')->with('id', $id)->with('first_step', $first_step)->with('broadcaster_details', $broadcaster_details);
    }

    public function postStep3($id)
    {
        $uploads = Utilities::uploadMedia();
        return response()->json(['success' => 'success']);
    }

    public function storeStep3_1($id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $broadcaster_details = Utilities::getBroadcasterDetails($broadcaster_id);
        $channel = $broadcaster_details[0]->channel_id;
        $channel_name = Utilities::switch_db('api')->select("SELECT * FROM campaignChannels where id = '$channel'");
        $get_uploaded_files = \DB::select("SELECT * from uploads where user_id = '$id' AND channel = '$channel'");
        $count_files = count($get_uploaded_files);
        if($count_files === 0){
            $msg = 'You have not uploaded any file(s) for '.$channel_name[0]->channel;
            Session::flash('error', $msg);
            return redirect()->back();
        }else{
            $remaining_file = 4 - $count_files;
            for ($i = 0; $i < $remaining_file; $i++){
                $insert_upload = \DB::table('uploads')->insert([
                    'user_id' => $id,
                    'time' => 00,
                    'channel' => $channel
                ]);
            }

        }

        return redirect()->route('campaign.create4', ['id' => $id, 'broadcaster' => $broadcaster_id]);

    }

    public function createStep4($id, $broadcaster)
    {
        ini_set('memory_limit','512M');

        $step1 = Session::get('first_step');

        Utilities::checkRequestSession($step1);

        $result_check = Utilities::checkRequestSession($step1);
        if($result_check === 'data_lost'){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }

        $ads_broad = Utilities::adslotFilter($step1, $broadcaster, null);

        $time = [15, 30, 45, 60];

        $data = \DB::select("SELECT * from uploads WHERE user_id = '$id'");
        $cart = \DB::select("SELECT * from carts WHERE user_id = '$id'");
        $total_cart = \DB::select("SELECT SUM(total_price) as total from carts where user_id = '$id'");
        $broadcaster_logo = $ads_broad['logo'];
        $positions = Utilities::switch_db('api')->select("SELECT * from filePositions where broadcaster_id = '$broadcaster'");

        $rate_card = Utilities::getRatecards($step1, $broadcaster);

        $adslots = $rate_card['adslot'];

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($rate_card['rate_card']);
        $perPage = 100;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('/agency/campaigns/campaign/step4/'.$id.'/'.$broadcaster);

        return view('broadcaster_module.campaigns.create_step4')->with('ratecards', $entries)->with('total_amount', $total_cart)->with('ads_broads', $ads_broad)
            ->with('cart', $cart)->with('datas', $data)->with('times', $time)->with('id', $id)
            ->with('broadcaster', $broadcaster)->with('broadcaster_logo', $broadcaster_logo)->with('positions', $positions)->with('ratings', $adslots);
    }

    public function postCart(Request $request)
    {
        $first = Session::get('first_step');
        $broadcaster_id = Session::get('broadcaster_id');
        $insert = Utilities::storeCart($request, $first, null, $broadcaster_id);

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
        $broadcaster_id = Session::get('broadcaster_id');

        $check_cart = \DB::select("SELECT * from carts where user_id = '$id'");
        if(count($check_cart) === 0){
            Session::flash('error', 'Your cart is empty...');
            return redirect()->back();
        }
        $query = [];
        $first = Session::get('first_step');
        $checkout = Utilities::getCheckout($id, $first, null, $broadcaster_id);


        return view('broadcaster_module.campaigns.checkout')->with('first_session', $first)
            ->with('calc', $checkout['calc'])
            ->with('day_part', $checkout['day_parts'])
            ->with('region', $checkout['regions'])
            ->with('target', $checkout['targets'])
            ->with('queries', $checkout['queries'])
            ->with('brand', $checkout['brands'])
            ->with('id', $id)
            ->with('user', $checkout['user'])
            ->with('broadcaster', $broadcaster_id);
    }

    public function removeMedia($walkins, $id)
    {
        $deleteUploads = \DB::delete("DELETE from uploads WHERE id = '$id' AND user_id = '$walkins'");
        if($deleteUploads){
            Session::flash('success', 'File deleted successfully...');
            return back();
        }else{
            Session::flash('error', 'Error deleting file...');
            return back();
        }
    }


    Public function postCampaign(Request $request, $walkins)
    {

        $save_campaign = $this->saveCampaign($request, $walkins);
        if($save_campaign === 'success'){
            $description = 'Campaign created by '.Session::get('broadcaster_id').' for '.$walkins;
            $user_activity = Api::saveActivity($walkins, $description);
            Session::flash('success', $this->campaign_success_message);
            return redirect()->route('broadcaster.campaign.hold');
        }else{
            Session::flash('error', 'There was problem creating campaign');
            return redirect()->back();
        }

    }

    public function removeCart($id)
    {
        $del = \DB::DELETE("DELETE FROM carts WHERE id = '$id'");
        Session::flash('success', 'Item deleted from cart successfully');
        return redirect()->back();
    }

    public function saveCampaign($request, $id)
    {

        $broadcaster_id = Session::get('broadcaster_id');
        $broadcaster_details = Utilities::getBroadcasterDetails($broadcaster_id);
        $api_db = Utilities::switch_db('api');
        $local_db = Utilities::switch_db('local');
        $first = Session::get('first_step');
        $queries = $local_db->select("SELECT * FROM carts WHERE user_id = '$id' AND broadcaster_id = '$broadcaster_id'");
        $ads = [];

        foreach ($queries as $query)
        {
            $ads[] = $query->adslot_id;
        }

        $file_array = [];
        $pay = [];
        $payDetails = [];
        $camp = [];
        $campDetails = [];
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

        $camp[] = Utilities::campaignInformation($campaign_id, $campaign_reference, $now);

        $calc = $local_db->select("SELECT SUM(total_price) as total_price FROM carts WHERE user_id = '$id' GROUP BY broadcaster_id");

        $campDetails[] = Utilities::campaignDetailsInformations($first, $campaign_id, $id, $now, $ads, null, null, $walkin_id, $broadcaster_id, $broadcaster_details, $queries);

        $check_time_adslots = Utilities::fetchTimeInCart($id, $broadcaster_id);
        foreach ($check_time_adslots as $check_time_adslot){
            if($check_time_adslot['initial_time_left'] < $check_time_adslot['time_bought']){
                $msg = 'You cannot proceed with the campaign creation because '.$check_time_adslot['from_to_time'].' for '.$check_time_adslot['broadcaster_name'].' isn`t available again';
                \Session::flash('info', $msg);
                return back();
            }
        }

        try {
            $this->storeCampaignsPaymentsFilesMposPayments($api_db, $campDetails, $camp, $queries, $id, $now, $broadcaster_id,
                $pay_id, $request, $first, $walkin_id, $calc, $campaign_id, $invoice_id,
                $invoice_number, $mpo_id, $file_array, $pay, $payDetails, $invoice, $invoiceDetails, $mpo, $mpoDetails);
        }catch (\Exception $e) {
            return 'error';
        }

        Session::forget('first_step');
        return 'success';
    }

    public function campaignDetails($id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $campaign_details = Utilities::campaignDetails($id, $broadcaster_id, null);
        $user_id = $campaign_details['campaign_det']['company_user_id'];
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where broadcaster = '$broadcaster_id' and user_id = '$user_id' ");
        $all_clients = Utilities::switch_db('api')->select("SELECT * FROM walkIns where broadcaster_id = '$broadcaster_id'");
        $media_mix_data = $this->getMediaMix($campaign_details);
        $campaign_price_graph = $this->getCampaignPriceGraph($campaign_details);

        return view('broadcaster_module.campaigns.details', compact('campaign_details', 'all_campaigns', 'all_clients', 'media_mix_data', 'campaign_price_graph'));
    }

    public function filterByUser($user_id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where broadcaster = '$broadcaster_id' and user_id = '$user_id' ");

        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id = '$broadcaster_id'");
        return (['campaign' => $all_campaigns, 'channel' => $media_chanel]);
    }

    public function getMediaMix($campaign_details)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $campaign_id = $campaign_details['campaign_det']['campaign_id'];

        $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as amount from paymentDetails where broadcaster ='$broadcaster_id'
                                                            AND payment_id = (SELECT id from payments where campaign_id = '$campaign_id')");
        $total_amount = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$campaign_id'");
        if($campaign_details['campaign_det']['channel'][0]->channel === 'TV'){
            $color = '#5281FE';
        }else{
            $color = '#00C4CA';
        }
        $media_mix_data[] = [
            'name' => $campaign_details['campaign_det']['channel'][0]->channel,
            'y' => (integer)(($payments[0]->amount / $total_amount[0]->total) * 100),
            'color' => $color
        ];

        return json_encode($media_mix_data);

    }

    public function getCampaignPriceGraph($campaign_details)
    {
        $campaign_price_data = [];
        $date = [];
        $broadcaster_id = Session::get('broadcaster_id');
        $campaign_id = $campaign_details['campaign_det']['campaign_id'];

        $payments = Utilities::switch_db('api')->select("SELECT amount from paymentDetails where broadcaster = '$broadcaster_id' AND payment_id IN (SELECT id FROM payments where campaign_id = '$campaign_id')");
        if($campaign_details['campaign_det']['channel'][0]->channel === 'TV'){
            $color = '#5281FE';
        }else{
            $color = '#00C4CA';
        }
        $campaign_price_data[] = [
            'color' => $color,
            'name' => $campaign_details['broadcasters'][0]->brand,
            'data' => array($payments[0]->amount),
            'stack' => $campaign_details['campaign_det']['channel'][0]->channel
        ];

        $campaign_price_date = Utilities::switch_db('api')->select("SELECT time_created from campaignDetails where campaign_id = '$campaign_id' AND broadcaster = '$broadcaster_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m-%d') ");

        $date[] = [date('Y-m-d', strtotime($campaign_price_date[0]->time_created))];

        return json_encode(['campaign_price_data' => $campaign_price_data, 'date' => $date]);


    }


    public function complianceFilter()
    {

        $broadcaster_id = Session::get('broadcaster_id');
        $campaign_id = request()->campaign_id;
        $start_date = date('Y-m-d', strtotime(request()->start_date));
        $stop_date = date('Y-m-d', strtotime(request()->stop_date));
        $compliance_data = [];

        $compliances = $this->getDateAndCampaignCompliances($campaign_id, $start_date, $stop_date, $broadcaster_id);
        $formatted_compliances = $this->formatToGraphFormat($compliances['compliance_data'], $campaign_id);

        foreach ($formatted_compliances['formatted_compliances'] as $compliance){
            if($compliance['stack'] === 'TV'){
                $color = '#5281FE';
            }else{
                $color = '#00C4CA';
            }
            $compliance_data[] = [
                'color' => $color,
                'name' => $compliance['brand'],
                'data' => $compliance['amount'],
                'stack' => $compliance['stack']
            ];
        }


        return response()->json(['date' => $compliances['dates'], 'compliance_data' => $compliance_data, 'percentage_compliance' => $formatted_compliances['percentage_compliance']]);
    }


    public function getDateAndCampaignCompliances($campaign_id, $start_date, $stop_date, $broadcaster_id)
    {
        $dates = [];
        $all_compliances_data = [];
        $date_compliances = Utilities::switch_db('api')->select("SELECT time_created from compliances where campaign_id = '$campaign_id'
                                                                    AND DATE_FORMAT(time_created, '%Y-%m-%d') BETWEEN '$start_date' AND '$stop_date' and broadcaster_id = '$broadcaster_id'
                                                                    GROUP BY DATE_FORMAT(time_created, '%Y-%m-%d') ");
        foreach ($date_compliances as $date_compliance){
            $date_created = date('Y-m-d', strtotime($date_compliance->time_created));
            //this query results to a multidimensional array
            $compliances = Utilities::switch_db('api')->select("SELECT IF(c.amount_spent IS NOT NULL, sum(c.amount_spent), 0) as amount,
                                                                              c.broadcaster_id, c.campaign_id, date_format(c.time_created, '%Y-%m-%d') as `time`,
                                                                               b.brand, c_c.channel as stack, c.channel from compliances as c INNER JOIN
                                                                               broadcasters as b ON b.id = c.broadcaster_id
                                                                               INNER JOIN campaignChannels as c_c ON c_c.id = c.channel where c.broadcaster_id = '$broadcaster_id'
                                                                               and b.id = '$broadcaster_id' and c.broadcaster_id = '$broadcaster_id'
                                                                               and c.campaign_id = '$campaign_id' and date_format(c.time_created, '%Y-%m-%d') = '$date_created' ");

            $all_compliances_data[] = [$compliances];
            $dates[] = [date('Y-m-d', strtotime($date_compliance->time_created))];
        }

        $flatened_compliances = array_flatten($all_compliances_data);

        return (['dates' => $dates, 'compliance_data' => $flatened_compliances]);
    }

    public function formatToGraphFormat($compliances, $campaign_id)
    {
        $compliances_array = json_decode(json_encode($compliances), true);

        $formatted_compliances = [];
        $total_spent = 0;
        foreach($compliances_array as $key=>$value){
            $total_spent += $value['amount'];
            if(!array_key_exists($value['broadcaster_id'],$formatted_compliances)){
                $formatted_compliances[$value['broadcaster_id']] = $value;
                unset($formatted_compliances[$value['broadcaster_id']]['amount']);
                $formatted_compliances[$value['broadcaster_id']]['date_reference'] =array();
                $formatted_compliances[$value['broadcaster_id']]['amount'] =array();

                array_push($formatted_compliances[$value['broadcaster_id']]['date_reference'],$value['time']);
                array_push($formatted_compliances[$value['broadcaster_id']]['amount'],(integer)$value['amount']);
            }else
            {
                array_push($formatted_compliances[$value['broadcaster_id']]['date_reference'],$value['time']);
                array_push($formatted_compliances[$value['broadcaster_id']]['amount'],(integer)$value['amount']);
            }

        }

        $total_amount_budgeted = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$campaign_id'");

        $percentage_compliance = round(($total_spent / $total_amount_budgeted[0]->total) * 100);

        return (['formatted_compliances' => $formatted_compliances, 'percentage_compliance' => $percentage_compliance]);
    }

    public function getCampaignOnHold()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.status, c_d.start_date, c_d.time_created, c_d.product,
                                                                        c_d.name, c_d.campaign_id, p.total, p.id as payment_id, b.name as brand_name, c_d.user_id as user_id,
                                                                        CONCAT(u.firstname,' ', u.lastname) as full_name, u.phone_number, u.email as email,
                                                                      c.campaign_reference from campaignDetails as c_d LEFT JOIN payments as p ON p.campaign_id = c_d.campaign_id
                                                                       LEFT JOIN campaigns as c ON c.id = c_d.campaign_id LEFT JOIN brands as b ON b.id = c_d.brand
                                                                       INNER JOIN users as u ON u.id = c_d.user_id
                                                                       where  c_d.broadcaster = '$broadcaster_id' AND c_d.agency = ''
                                                                       and c_d.status = 'on_hold' and c_d.adslots  > 0 ORDER BY c_d.time_created DESC");

        $campaigns = Utilities::getCampaignDatatablesforCampaignOnHold($all_campaigns);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($campaigns);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $campaigns = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $campaigns->setPath('data');

        return view('broadcaster_module.campaigns.campaign_onhold', compact('campaigns'));
    }

    public function updateCampaign($payment_method, $campaign_id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $check_campaign_start_date = Utilities::checkIfCampaignStartDateHasReached($campaign_id, $broadcaster_id, null);
        if($check_campaign_start_date == 'error'){
            Session::flash('error', 'Campaign cant be submitted because the start date has exceeded the current date');
            return redirect()->back();
        }
        $api_db = Utilities::switch_db('api');
        $single_campaign = $api_db->select("SELECT c_d.campaign_id, p.id as payment_id from campaignDetails as c_d
                                            INNER JOIN payments as p ON p.campaign_id = c_d.campaign_id
                                            where  c_d.broadcaster = '$broadcaster_id'
                                            and c_d.campaign_id = '$campaign_id' and c_d.adslots  > 0 ORDER BY c_d.time_created DESC");

        $campaign_id = $single_campaign[0]->campaign_id;
        $payment_id = $single_campaign[0]->payment_id;

        try {
            $api_db->transaction(function () use ($api_db, $campaign_id, $payment_method, $payment_id) {
                $api_db->update("UPDATE campaignDetails set status = 'pending' WHERE campaign_id = '$campaign_id'");
                $api_db->update("UPDATE paymentDetails set payment_method = '$payment_method', payment_status = 1 where payment_id = '$payment_id'");
                $api_db->update("UPDATE invoiceDetails set status = 1 WHERE invoice_id = (SELECT id from invoices WHERE campaign_id = '$campaign_id')");
            });
        }catch (\Exception $e) {
            return 'error';
        }
        return 'success';

    }

    public function submitCampaignWithOtherPaymentOption(Request $request, $campaign_id)
    {
        $save_campaign = $this->updateCampaign($request->payment_option, $campaign_id);
        if($save_campaign === 'success'){
            $description = 'Campaign created by '.Session::get('broadcaster_id').' for successfully';
            Api::saveActivity(Session::get('broadcaster_id'), $description);
            Session::flash('success', $this->campaign_success_message);
            return redirect()->route('broadcaster.campaign_management');
        }else{
            Session::flash('error', 'There was problem creating campaign');
            return redirect()->back();
        }
    }

    public function payCampaign(Request $request)
    {
        $insert = [
            'id' => uniqid(),
            'user_id' => $request->user_id,
            'reference' => $request->reference,
            'amount' => $request->total,
            'status' => 'PENDING',
        ];

        Utilities::switch_db('api')->table('transactions')->insert($insert);

        $response = Paystack::query_api_transaction_verify($request->reference);

        if($response['status'] === true){

            $amount = ($response['data']['amount']/100);
            $card = $response['data']['authorization']['card_type'];
            $status = $response['data']['status'];
            $message = $response['message'];
            $reference = $response['data']['reference'];
            $ip_address = $response['data']['ip_address'];
            $fees = $response['data']['fees'];
            $user_id = $request->user_id;
            $type = 'PAID FOR CAMPAIGN';

            $req = [
                'payment' => 'Card',
                'total' => $amount,
                'campaign_id' => $request->campaign_id
            ];
            $request = (object)$req;

            try {
                Utilities::switch_db('api')->transaction(function () use($card, $ip_address, $fees, $type, $message, $reference, $request, $amount, $user_id) {
                    Utilities::switch_db('api')->select("UPDATE transactions SET card_type = '$card', status = 'SUCCESSFUL', ip_address = '$ip_address',
                                                                    fees = '$fees', `type` = '$type', message = '$message' WHERE reference = '$reference'");
                    $save_campaign = $this->updateCampaign($request->payment, $request->campaign_id);
                    if($save_campaign === 'success') {
                        $description = 'Payment of ' . $amount . ' to ' . Session::get('broadcaster_id') . ' by ' . $user_id . ' For Campaign';
                        Api::saveActivity($user_id, $description);
                    }
                });
            }catch (\Exception $e){
                Session::flash('error', 'There was problem creating campaign');
                return redirect()->back();
            }

            $msg = 'Your payment of '. $amount.' is successful and campaign has been submitted';
            Session::flash('success', $msg);
            return redirect()->route('broadcaster.campaign_management');

        } else {
            Session::flash('error', 'Sorry, something went wrong! Please contact the Administrator or Bank.');
            return redirect()->back();
        }

    }

    public function updateCampaignInformation(CampaignInformationUpdateRequest $request, $campaign_id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $check_campaign_start_date = Utilities::checkIfCampaignStartDateHasReached($campaign_id, $broadcaster_id, null);
        if($check_campaign_start_date == 'error'){
            Session::flash('error', 'Campaign cant be submitted because the start date has exceeded the current date');
            return redirect()->back();
        }
        Utilities::switch_db('api')->update("UPDATE campaignDetails set name = '$request->name', product = '$request->product' WHERE campaign_id = '$campaign_id'");

        Session::flash('success', 'Campaign Information updated');
        return redirect()->back();
    }

    public static function storeCampaignsPaymentsFilesMposPayments($api_db, $campDetails, $camp, $queries, $id, $now, $broadcaster_id,
                                                                   $pay_id, $request, $first, $walkin_id, $calc, $campaign_id, $invoice_id,
                                                                   $invoice_number, $mpo_id, $file_array, $pay, $payDetails, $invoice, $invoiceDetails, $mpo, $mpoDetails)
    {

        $api_db->transaction(function () use ($api_db, $campDetails, $camp, $queries, $id, $now, $broadcaster_id,
        $pay_id, $request, $first, $walkin_id, $calc, $campaign_id, $invoice_id,
        $invoice_number, $mpo_id, $file_array, $pay, $payDetails, $invoice, $invoiceDetails, $mpo, $mpoDetails) {
            $api_db->table('campaignDetails')->insert($campDetails);
            $api_db->table('campaigns')->insert($camp);
            $campaign_details = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE id='$campaign_id'");
            foreach($queries as $query)
            {
                $file_array = Utilities::campaignFileInformation($campaign_details, $query, $id, $now, null, $broadcaster_id);
                SelectedAdslot::create($file_array);
            }
            $pay[] = Utilities::campaignPaymentInformation($pay_id, $campaign_details, $request, $now, $first);
            $payDetails[] = Utilities::campaignPaymentDetailsInformation($pay_id, $request, null, $walkin_id, $now, null, $first, $calc, $broadcaster_id);
            $api_db->table('payments')->insert($pay);
            $api_db->table('paymentDetails')->insert($payDetails);
            $payment_id = $api_db->select("SELECT id from payments WHERE id='$pay_id'");
            $invoice[] = Utilities::campaignInvoiceInformation($invoice_id, $campaign_details, $invoice_number, $payment_id);
            $invoiceDetails[] = Utilities::campaignInvoiceDetailsInformation($invoice_id, $id, $invoice_number, null, $walkin_id, null, $broadcaster_id, $calc);
            $api_db->table('invoices')->insert($invoice);
            $api_db->table('invoiceDetails')->insert($invoiceDetails);
            $mpo[] = Utilities::campaignMpoInformation($mpo_id, $campaign_details, $invoice_number);
            $mpoDetails[] = Utilities::campaignMpoDetailsInformation($mpo_id, null, null, $broadcaster_id);
            $api_db->table('mpos')->insert($mpo);
            $api_db->table('mpoDetails')->insert($mpoDetails);
            foreach ($queries as $query){
                if(!empty($query->filePosition_id)){
                    $api_db->update("UPDATE adslot_filePositions set select_status = 1
                                    WHERE adslot_id = '$query->adslot_id' AND broadcaster_id = '$broadcaster_id'");
                }
                $get_slots = $api_db->select("SELECT * from adslots WHERE id = '$query->adslot_id'");
                $slots_id = $get_slots[0]->id;
                $time_difference = $get_slots[0]->time_difference;
                $time_used = $get_slots[0]->time_used;
                $time = $query->time;
                $new_time_used = $time_used + $time;
                if($time_difference === $new_time_used){
                    $slot_status = 1;
                }else{
                    $slot_status = 0;
                }
                $api_db->update("UPDATE adslots SET time_used = '$new_time_used', is_available = '$slot_status' WHERE id = '$slots_id'");
            }
            \DB::delete("DELETE FROM carts WHERE user_id = '$id'");
            \DB::delete("DELETE FROM uploads WHERE user_id = '$id'");
        });

    }

    /**
     * I hate this, we need to resolve
     */
    private function formatImageUrl($image_url)
    {
        return encrypt(Utilities::convertCloudinaryHttpToHttps(decrypt($image_url)));

    }

}
