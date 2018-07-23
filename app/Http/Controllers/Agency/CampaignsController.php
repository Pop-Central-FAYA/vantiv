<?php

namespace Vanguard\Http\Controllers\Agency;

use Carbon\Carbon;
use Hamcrest\Util;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use JD\Cloudder\Facades\Cloudder;
use Pbmedia\LaravelFFMpeg\FFMpeg;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Session;

class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('agency.campaigns.active_campaign');
    }


    public function getData(DataTables $dataTables, Request $request)
    {
        //campaigns
        $agency_campaigns = [];
        $agency_id = Session::get('agency_id');
        $today_date = date("Y-m-d");

        if($request->has('start_date') && $request->has('stop_date')){
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;

            $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE agency = '$agency_id' AND adslots > 0 AND start_date <= '$today_date' AND stop_date > '$today_date' AND time_created BETWEEN '$start_date' AND '$stop_date' GROUP BY campaign_id ORDER BY time_created DESC");
            $j = 1;
            foreach ($all_campaign as $cam)
            {

                $campaign_reference = Utilities::switch_db('api')->select("SELECT * from campaigns where id = '$cam->campaign_id'");
//            $today = strtotime(date('Y-m-d'));
                $today = date("Y-m-d");
                if(strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)){
                    $status = 'Finished';
                }elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)){
                    $status = 'Active';
                }else{
                    $now = strtotime($today);
                    $your_date = strtotime($cam->start_date);
                    $datediff = $your_date - $now;
                    $new_day =  round($datediff / (60 * 60 * 24));
                    $status = 'Pending';
                }
                $brand = Utilities::switch_db('api')->select("SELECT `name` as brand_name from brands where id = '$cam->brand'");
                $pay = Utilities::switch_db('api')->select("SELECT total from payments where campaign_id = '$cam->campaign_id'");
                $agency_campaigns[] = [
                    'id' => $campaign_reference[0]->campaign_reference,
                    'camp_id' => $cam->campaign_id,
                    'name' => $cam->name,
                    'brand' => ucfirst($brand[0]->brand_name),
                    'product' => $cam->product,
                    'date_created' => date('M j, Y', strtotime($cam->time_created)),
                    'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                    'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                    'adslots' => $cam->adslots,
                    'budget' => number_format($pay[0]->total, 2),
                    'status' => $status
                ];
                $j++;
            }

            return $dataTables->collection($agency_campaigns)
                ->addColumn('name', function ($agency_campaigns) {
                    return '<a href="'.route('agency.campaign.details', ['id' => $agency_campaigns['camp_id']]).'">'.$agency_campaigns['name'].'</a>';
                })
                ->editColumn('status', function ($agency_campaigns){
                    if($agency_campaigns['status'] === "Finished"){
                        return '<span class="span_state status_danger">Finished</span>';
                    }elseif ($agency_campaigns['status'] === "Active"){
                        return '<span class="span_state status_success">Active</span>';
                    }else{
                        return '<span class="span_state status_pending">Pending</span>';
                    }
                })
                ->rawColumns(['status' => 'status', 'name' => 'name'])
                ->addIndexColumn()
                ->make(true);
        }

        $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE agency = '$agency_id' AND start_date <= '$today_date' AND stop_date > '$today_date' AND adslots > 0 GROUP BY campaign_id ORDER BY time_created DESC");
        $j = 1;
        foreach ($all_campaign as $cam)
        {

            $campaign_reference = Utilities::switch_db('api')->select("SELECT * from campaigns where id = '$cam->campaign_id'");
//            $today = strtotime(date('Y-m-d'));
            $today = date("Y-m-d");
            if(strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)){
                $status = 'Finished';
            }elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)){
                $status = 'Active';
            }else{
                $now = strtotime($today);
                $your_date = strtotime($cam->start_date);
                $datediff = $your_date - $now;
                $new_day =  round($datediff / (60 * 60 * 24));
                $status = 'Pending';
            }
            $brand = Utilities::switch_db('api')->select("SELECT `name` as brand_name from brands where id = '$cam->brand'");
            $pay = Utilities::switch_db('api')->select("SELECT total from payments where campaign_id = '$cam->campaign_id'");
            $agency_campaigns[] = [
                'id' => $campaign_reference[0]->campaign_reference,
                'camp_id' => $cam->campaign_id,
                'name' => $cam->name,
                'brand' => ucfirst($brand[0]->brand_name),
                'product' => $cam->product,
                'date_created' => date('M j, Y', strtotime($cam->time_created)),
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'adslots' => $cam->adslots,
                'budget' => number_format($pay[0]->total, 2),
                'status' => $status
            ];
            $j++;
        }

        return $dataTables->collection($agency_campaigns)
            ->addColumn('name', function ($agency_campaigns) {
                return '<a href="'.route('agency.campaign.details', ['id' => $agency_campaigns['camp_id']]).'">'.$agency_campaigns['name'].'</a>';
            })
            ->editColumn('status', function ($agency_campaigns){
                if($agency_campaigns['status'] === "Finished"){
                    return '<span class="span_state status_danger">Finished</span>';
                }elseif ($agency_campaigns['status'] === "Active"){
                    return '<span class="span_state status_success">Active</span>';
                }else{
                    return '<span class="span_state status_pending">Pending</span>';
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
    public function allClient()
    {
        return view('agency.campaigns.client.index');
    }

    public function clientData(DataTables $dataTables)
    {
        $j = 1;
        $cl = [];
        $agency_id = \Session::get('agency_id');
        $all_clients = Utilities::switch_db('api')->select("SELECT * from walkIns WHERE agency_id = '$agency_id'");
        foreach ($all_clients as $all) {
            $clients = \DB::select("SELECT * from users where id = '$all->user_id'");
            $cl[] = [
                'id' => $j,
                'user_id' => $clients && $clients[0] ? $clients[0]->id : 1,
                'name' => $clients && $clients[0] ? $clients[0]->last_name . ' ' . $clients[0]->first_name : '',
                'email' => $clients && $clients[0] ? $clients[0]->email : '',
                'phone' => $clients && $clients[0] ? $clients[0]->phone : '',
            ];
            $j++;
        }

        return $dataTables->collection($cl)
            ->addColumn('create_campaign', function ($cl) {
                return '<a href="' . route('agency_campaign.step1', $cl['user_id']) .'" class="btn btn-success btn-xs" > Create Campaign </a>';
            })
            ->rawColumns(['create_campaign' => 'create_campaign'])->addIndexColumn()
            ->make(true);

    }

    public function getStep1()
    {
        if(Session::get('first_step')){
            $first_step = Session::get('first_step');
            $brands = Utilities::switch_db('api')->select("SELECT * from brands WHERE walkin_id = '$first_step->clients'");
        }
        $agency_id = Session::get('agency_id');
        $industry = Utilities::switch_db('api')->select("SELECT * from sectors");
        $sub_industries = Utilities::switch_db('api')->select("select * from subSectors");
        $chanel = Utilities::switch_db('api')->select("SELECT * from campaignChannels");
        $clients = Utilities::switch_db('api')->select("SELECT * from walkIns where agency_id = '$agency_id'");

        $day_parts = Utilities::switch_db('api')->select("SELECT * from dayParts");
        $region = Utilities::switch_db('api')->select("SELECT * from regions");
        $target = Utilities::switch_db('api')->select("SELECT * from targetAudiences");

        Api::validateCampaign();
        return view('agency.campaigns.create1')->with('industries', $industry)
            ->with('channels', $chanel)
            ->with('regions', $region)
            ->with('day_parts', $day_parts)
            ->with('targets', $target)
            ->with('clients', $clients)
            ->with('first_step', Session::get('first_step') ? $first_step : '')
            ->with('brands', Session::get('first_step') ? $brands : '')
            ->with('sub_industries', $sub_industries);
    }

    public function postStep1(Request $request)
    {
        $this->validate($request, [
            'clients' => 'required',
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

        if($request->min_age < 0 || $request->max_age < 0){
            Session::flash('error', 'The minimum or maximum age cannot have a negetive value');
            return back();
        }

        if($request->min_age > $request->max_age){
            Session::flash('error', 'The minimum age cannot be greater than the maximum age');
            return back();
        }

        $walkin = Utilities::switch_db('api')->select("SELECT * from walkIns where id = '$request->clients'");
        $user_id = $walkin[0]->user_id;

        if(strtotime($request->end_date) < strtotime($request->start_date)){
            return redirect()->back()->with('error', 'Start Date cannot be greater than End Date');
        }

        $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$user_id' AND agency_id = '$agency_id'");
        $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$user_id'");
        $del_file_position = Utilities::switch_db('api')->delete("DELETE FROM adslot_filePositions where select_status = 0");

        if (strtotime($request->end_date) < strtotime($request->start_date)) {
            Session::flash('error', 'Start Date cannot be greater than End Date');
            return redirect()->back();
        }

        $step1_req = ((object) $request->all());
        session(['first_step' => $step1_req]);

        return redirect()->route('agency_campaign.step2', ['id' => $user_id])
            ->with('step_1', Session::get('first_step'))
            ->with('id', $user_id);
    }

    public function getStep2($id)
    {
        $step1 = Session::get('first_step');
        if (!$step1) {
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return redirect()->back();
        }

        $day_parts = "'".implode("','" ,$step1->dayparts)."'";
        $region = "'".implode("','", $step1->region)."'";
        $target_audience = "'".implode("','", $step1->target_audience)."'";
        $channel = "'".implode("','", $step1->channel)."'";
        $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience IN ($target_audience) AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 AND channels IN ($channel) group by broadcaster");
        $ads_broad = [];
        foreach ($adslots as $adslot)
        {
            $broad = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$adslot->broadcaster'");
            $ads_broad[] = [
                'broadcaster' => $adslot->broadcaster,
                'count_adslot' => $adslot->all_slots,
                'boradcaster_brand' => $broad[0]->brand,
                'logo' => $broad[0]->image_url,
            ];
        }

        return view('agency.campaigns.create2')->with('adslots', $ads_broad)
            ->with('id', $id);
    }

    public function getStep3($id)
    {
        $delete_uploads_without_files = \DB::delete("DELETE from uploads where user_id = '$id' AND time = 0");

        $first_step = Session::get('first_step');
        if (!$first_step) {
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return redirect()->back();
        }
        $day_parts = "'".implode("','" ,$first_step->dayparts)."'";
        $region = "'".implode("','", $first_step->region)."'";
        $target_audience = "'".implode("','", $first_step->target_audience)."'";
        $channel = "'".implode("','", $first_step->channel)."'";
        $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $first_step->min_age AND max_age <= $first_step->max_age AND target_audience IN ($target_audience) AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 AND channels IN ($channel) group by broadcaster");
//        dd($adslots);
        $ads_broad = [];
        foreach ($adslots as $adslot)
        {
            $broad = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$adslot->broadcaster'");
            $ads_broad[] = [
                'broadcaster' => $adslot->broadcaster,
                'count_adslot' => $adslot->all_slots,
                'boradcaster_brand' => $broad[0]->brand,
                'logo' => $broad[0]->image_url,
            ];
        }

        if(count($ads_broad) === 0){
            Session::flash('error', 'No adslots matches your filter criteria, please go back and re-adjust your requirements');
            return redirect()->back();
        }

        return view('agency.campaigns.create3')->with('id', $id)->with('first_step', $first_step);
    }

    public function postStep3($id)
    {
        $id = request()->user_id;
        if(round((integer)request()->duration) > (integer)request()->time_picked){
            return response()->json(['error' => 'error']);
        }

        $check_file = \DB::select("SELECT * from uploads where user_id = '$id'");
        if(count($check_file) > 4){
            return response()->json(['error_number' => 'error_number']);
        }
        $image_url = encrypt(request()->image_url);
        $time = request()->time_picked;
        $channel = request()->channel;
        $format = request()->file_format;
        if (request()->image_url) {

            $check_image = \DB::select("SELECT * from uploads where time = '$time' AND channel = '$channel' AND user_id = '$id'");
            if(count($check_image) === 1){
                return response()->json(['error_check_image' => 'error_check_image']);
            }

            $insert_upload = \DB::table('uploads')->insert([
                'user_id' => $id,
                'time' => $time,
                'uploads' => $image_url,
                'file_name' => $time.'_'.$format.'_'.request()->file_name,
                'file_code' => request()->public_id,
                'channel' => $channel,
                'format' => $format
            ]);

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
        }

        return redirect()->route('agency_campaign.step3_2', ['id' => $id]);

    }

    public function getStep3_2($id)
    {
        $first_step = Session::get('first_step');
        $day_parts = "'".implode("','" ,$first_step->dayparts)."'";
        $region = "'".implode("','", $first_step->region)."'";
        $target_audience = "'".implode("','", $first_step->target_audience)."'";
        $channel = "'".implode("','", $first_step->channel)."'";
        $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $first_step->min_age AND max_age <= $first_step->max_age AND target_audience IN ($target_audience) AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 AND channels IN ($channel) group by broadcaster");
        $ads_broad = [];

        foreach ($adslots as $adslot)
        {
            $broad = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$adslot->broadcaster'");
            $ads_broad[] = [
                'broadcaster' => $adslot->broadcaster,
                'count_adslot' => $adslot->all_slots,
                'boradcaster_brand' => $broad[0]->brand,
                'logo' => $broad[0]->image_url,
            ];
        }
        return view('agency.campaigns.create3_2')->with('adslot_search_results', $ads_broad)->with('id', $id);
    }

    public function getStep4($id, $broadcaster)
    {

        $rate_card = [];
        $step1 = Session::get('first_step');
        if (!$step1) {
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return redirect()->back();
        }
        $day_parts = "'".implode("','" ,$step1->dayparts)."'";
        $region = "'".implode("','", $step1->region)."'";
        $target_audience = "'".implode("','", $step1->target_audience)."'";
        $channel = "'".implode("','", $step1->channel)."'";
        $adslots_count = Utilities::switch_db('api')->select("SELECT * FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND channels IN ($channel) AND target_audience IN ($target_audience) AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 AND broadcaster = '$broadcaster'");
        //dd($adslots_count);
        $result = count($adslots_count);

        $ratecards = Utilities::switch_db('api')->select("SELECT * from rateCards WHERE broadcaster = '$broadcaster' AND id IN (SELECT rate_card FROM adslots where min_age >= $step1->min_age
                                                            AND max_age <= $step1->max_age
                                                            AND target_audience IN ($target_audience)
                                                            AND day_parts IN ($day_parts) AND region IN ($region)
                                                            AND is_available = 0 AND broadcaster = '$broadcaster') ");

        foreach ($ratecards as $ratecard){
            $day = Utilities::switch_db('api')->select("SELECT * from days where id = '$ratecard->day'");
            $hourly_range = Utilities::switch_db('api')->select("SELECT * from hourlyRanges where id = '$ratecard->hourly_range_id'");
            $adslots = Utilities::switch_db('api')->select("SELECT * from adslots WHERE rate_card = '$ratecard->id' AND is_available = 0");
            $price = Utilities::switch_db('api')->select("SELECT * from adslotPrices WHERE adslot_id IN (SELECT id from adslots WHERE rate_card = '$ratecard->id')");
            $rate_card[] = [
                'id' => $ratecard->id,
                'hourly_range' => $hourly_range[0]->time_range,
                'day' => $day[0]->day,
                'adslot' => $adslots,
                'price' => $price,
            ];
        }

        $time = [15, 30, 45, 60];

        $data = \DB::select("SELECT * from uploads WHERE user_id = '$id'");
        $cart = \DB::select("SELECT * from carts WHERE user_id = '$id'");
        $total_cart = \DB::select("SELECT SUM(total_price) as total from carts where user_id = '$id'");
        $broadcaster_logo = Utilities::switch_db('api')->select("SELECT image_url from broadcasters where id = '$broadcaster'");
        $positions = Utilities::switch_db('api')->select("SELECT * from filePositions where broadcaster_id = '$broadcaster'");

        $adslots_broadcasters = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience IN ($target_audience) AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 AND channels IN ($channel) group by broadcaster");
        $ads_broad = [];
        foreach ($adslots_broadcasters as $adslots_broadcaster)
        {
            $broad = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$adslots_broadcaster->broadcaster'");
            $ads_broad[] = [
                'broadcaster' => $adslots_broadcaster->broadcaster,
                'count_adslot' => $adslots_broadcaster->all_slots,
                'broadcaster_brand' => $broad[0]->brand,
                'logo' => $broad[0]->image_url,
            ];
        }


        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($rate_card);
        $perPage = 100;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('/agency/campaigns/campaign/step4/'.$id.'/'.$broadcaster);

        return view('agency.campaigns.create4')->with('ratecards', $entries)->with('total_amount', $total_cart)->with('ads_broads', $ads_broad)->with('result', $result)->with('cart', $cart)->with('datas', $data)->with('times', $time)->with('id', $id)->with('broadcaster', $broadcaster)->with('broadcaster_logo', $broadcaster_logo)->with('positions', $positions);
    }

    public function postCart(Request $request)
    {

        if((int)$request->position != ''){
            $get_percentage = Utilities::switch_db('api')->select("SELECT percentage from filePositions where id = '$request->position'");
            $percentage = $get_percentage[0]->percentage;
            $percentage_price = (($percentage / 100) * (int)$request->price);
            $new_price = $percentage_price + (int)$request->price;

        }else{
            $new_price = (int)$request->price;
            $percentage = 0;
        }

        $price = $request->price;
        $file = $request->file;
        $time = $request->time;
        $hourly_range = $request->range;
        $user = $request->walkins;
        $adslot_id = $request->adslot_id;
        $position = $request->position;
        $file_name = $request->file_name;
        $public_id = $request->file_code;
        $broadcaster = $request->broadcaster;
        $file_format = $request->file_format;
        $agency = Session::get('agency_id');
        $ip = \Request::ip();

        //check if the fileposition is picked
        $check_pos = Utilities::switch_db('api')->select("SELECT * from adslot_filePositions where broadcaster_id = '$broadcaster' AND adslot_id = '$adslot_id' AND filePosition_id = '$position'");
        if(count($check_pos) === 1){
            return response()->json(['file_error' => 'file_error']);
        }

        if((int)$request->position != '') {
            $id = uniqid();
            $insert_file = Utilities::switch_db('api')->insert("INSERT into adslot_filePositions (id, adslot_id,filePosition_id, status, select_status, broadcaster_id) VALUES ('$id', '$adslot_id', '$position', 1, 0, '$broadcaster')");
        }

        $check = \DB::select("SELECT * from carts where adslot_id = '$adslot_id' and user_id = '$user' AND agency_id = '$agency' and filePosition_id = '$position' and filePosition_id != ''");
        if(count($check) === 1){
            return response()->json(['error' => 'error']);
        }

        //check if the budget has not been reached
        $check_cart = \DB::select("SELECT SUM(total_price) as total from carts where user_id = '$user' AND agency_id = '$agency'");
        $new_total = (integer)$new_price + $check_cart[0]->total;
        if((integer)$new_total > (integer)Session::get('first_step')->campaign_budget){
            return response()->json(['budget_exceed_error' => 'budget_exceed_error']);
        }


        $insert = \DB::insert("INSERT INTO carts (user_id, broadcaster_id, price, ip_address, file, from_to_time, `time`, adslot_id, percentage, total_price, filePosition_id, status, agency_id, file_name, public_id, format) VALUES ('$user','$broadcaster','$price','$ip','$file','$hourly_range','$time','$adslot_id', '$percentage', '$new_price', '$position', 1, '$agency', '$file_name', '$public_id', '$file_format')");

        if($insert){
            return response()->json(['success' => 'success']);
        }else{
            return response()->json(['failure' => 'failure']);
        }
    }

    public function checkout($id)
    {
        $check_cart = \DB::select("SELECT * from carts where user_id = '$id'");
        if(count($check_cart) === 0){
            Session::flash('error', 'Your cart is empty...');
            return redirect()->back();
        }
        $agency_id = Session::get('agency_id');
        $query = [];
        $first = Session::get('first_step');
        $day_parts = implode("','" ,$first->dayparts);
        $region = implode("','", $first->region);
        $target_audience = implode(",", $first->target_audience);
        $brands = Utilities::switch_db('api')->select("SELECT name from brands where id = '$first->brand'");
        $day_partss = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id IN ('$day_parts') ");
        $targets = Utilities::switch_db('api')->select("SELECT audience from targetAudiences where id = '$target_audience'");
        $regions = Utilities::switch_db('api')->select("SELECT region from regions where id IN ('$region') ");
        $calc = \DB::select("SELECT SUM(total_price) as total_price FROM carts WHERE user_id = '$id' and agency_id = '$agency_id'");
//        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$id'");
        $query_carts = \DB::select("SELECT * FROM carts WHERE user_id = '$id' AND agency_id = '$agency_id'");
        foreach ($query_carts as $query_cart){
            $position = Utilities::switch_db('api')->select("SELECT * from filePositions where id = '$query_cart->filePosition_id'");
            $broadcaster = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$query_cart->broadcaster_id'");
            $query[] = [
                'id' => $query_cart->id,
                'from_to_time' => $query_cart->from_to_time,
                'time' => $query_cart->time,
                'price' => $query_cart->price,
                'percentage' => $query_cart->percentage,
                'position' => $position ? $position[0]->position : 'No Position',
                'total_price' => $query_cart->total_price,
                'broadcaster_logo' => $broadcaster[0]->image_url,
                'broadcaster_brand' => $broadcaster[0]->brand,
            ];
        }

        return view('agency.campaigns.checkout')->with('first_session', $first)
            ->with('calc', $calc)
            ->with('day_part', $day_partss)
            ->with('region', $regions)
            ->with('target', $targets)
            ->with('queries', $query)
            ->with('brand', $brands)
            ->with('id', $id)
            ->with('broadcaster', $broadcaster);
    }

    public function removeCart($id)
    {
        $del = \DB::select("DELETE FROM carts WHERE id = '$id'");
        Session::flash('success', 'Item deleted from cart successfully');
        return redirect()->back();
    }

    Public function postCampaign(Request $request, $id)
    {
        $agency_id = Session::get('agency_id');
        $first = Session::get('first_step');
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$id' AND agency_id = '$agency_id'");
        $ads = [];

        $user_id = $id;

        foreach ($query as $q) {
            $ads[] = $q->adslot_id;
        }
        $data = \DB::select("SELECT * from uploads WHERE user_id = '$id'");
        $group_datas = \DB::select("SELECT SUM(total_price) as total, COUNT(id) as total_slot, broadcaster_id from carts where user_id = '$id' and agency_id = '$agency_id' GROUP BY broadcaster_id");

        $request->all();
        $new_q = [];
        $pay = [];
        $payDets = [];
        $camp = [];
        $campDets = [];
        $invoice = [];
        $invDets = [];
        $mpo = [];
        $mpoDet = [];
        $i = 0;

        $adssss = implode(',' ,$ads);
        $campaign_id = uniqid();
        $pay_id = uniqid();
        $invoice_id = uniqid();
        $mpo_id = uniqid();
        $campaign_reference = Utilities::generateReference();
        $invoice_number = Utilities::generateReference();
        $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns where user_id = '$id'");
        $now = strtotime(Carbon::now('Africa/Lagos'));

        $camp[] = [
            'id' => $campaign_id,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'campaign_reference' => $campaign_reference
        ];

        foreach ($group_datas as $group_data){
            $campDets[] = [
                'id' => uniqid(),
                'campaign_id' => $campaign_id,
                'user_id' => $id,
                'channel' => "'". implode("','" ,$first->channel) . "'",
                'brand' => $first->brand,
                'start_date' => date('Y-m-d', strtotime($first->start_date)),
                'stop_date' => date('Y-m-d', strtotime($first->end_date)),
                'name' => $first->campaign_name,
                'product' => $first->product,
                'day_parts' => "'". implode("','" ,$first->dayparts) . "'",
                'target_audience' => "'". implode("','" ,$first->target_audience) . "'",
                'region' => "'". implode("','" ,$first->region) . "'",
                'min_age' => (integer)$first->min_age,
                'max_age' => (integer)$first->max_age,
                'industry' => $first->industry,
                'adslots' => $group_data->total_slot,
                'walkins_id' => $walkin_id[0]->id,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
                'adslots_id' => "'". implode("','" ,$ads) . "'",
                'agency' => $agency_id,
                'agency_broadcaster' => $group_data->broadcaster_id,
                'broadcaster' => $group_data->broadcaster_id,
                'sub_industry' => $first->sub_industry,
            ];

            $check_time_adslots = Utilities::fetchTimeInCart($id, $group_data->broadcaster_id);
            foreach ($check_time_adslots as $check_time_adslot){
                if($check_time_adslot['initial_time_left'] < $check_time_adslot['time_bought']){
                    $msg = 'You cannot proceed with the campaign creation because '.$check_time_adslot['from_to_time'].' for '.$check_time_adslot['broadcaster_name'].' isn`t available again';
                    \Session::flash('info', $msg);
                    return back();
                }
            }
        }

        $save_campaign = Utilities::switch_db('api')->table('campaigns')->insert($camp);
        $save_campaign_details = Utilities::switch_db('api')->table('campaignDetails')->insert($campDets);

        if($save_campaign && $save_campaign_details){
            $camp_id = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE id='$campaign_id'");
            foreach($query as $q)
            {

                $new_q[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'file_name' => $q->file_name,
                    'file_url' => $q->file,
                    'adslot' => $q->adslot_id,
                    'user_id' => $id,
                    'file_code' => Utilities::generateReference(),
                    'time_created' => date('Y-m-d H:i:s', $now),
                    'time_modified' => date('Y-m-d H:i:s', $now),
                    'agency_id' => $agency_id,
                    'agency_broadcaster' => $q->broadcaster_id,
                    'time_picked' => $q->time,
                    'broadcaster_id' => $q->broadcaster_id,
                    'public_id' => $q->public_id,
                    'format' => $q->format
                ];
            }

            $pay[] = [
                'id' => $pay_id,
                'campaign_id' => $camp_id[0]->id,
                'campaign_reference' => $camp_id[0]->campaign_reference,
                'total' => $request->total,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
                'campaign_budget' => $first->campaign_budget
            ];

            foreach ($group_datas as $group_data){
                $payDets[] = [
                    'id' => uniqid(),
                    'payment_id' => $pay_id,
                    'payment_method' => $request->payment,
                    'amount' => (integer) $group_data->total,
                    'walkins_id' => $walkin_id[0]->id,
                    'time_created' => date('Y-m-d H:i:s', $now),
                    'time_modified' => date('Y-m-d H:i:s', $now),
                    'agency_id' => $agency_id,
                    'agency_broadcaster' => $group_data->broadcaster_id,
                    'broadcaster' => $group_data->broadcaster_id,
                    'campaign_budget' => $first->campaign_budget
                ];
            }

            $save_payment = Utilities::switch_db('api')->table('payments')->insert($pay);

            $save_payment_details = Utilities::switch_db('api')->table('paymentDetails')->insert($payDets);

            $save_file = Utilities::switch_db('api')->table('files')->insert($new_q);

            if ($save_payment && $save_file && $save_payment_details) {

                $payment_id = Utilities::switch_db('api')->select("SELECT id from payments WHERE id='$pay_id'");

                $invoice[] = [
                    'id' => $invoice_id,
                    'campaign_id' => $camp_id[0]->id,
                    'campaign_reference' => $camp_id[0]->campaign_reference,
                    'invoice_number' => $invoice_number,
                    'payment_id' => $payment_id[0]->id,
                ];

                foreach ($group_datas as $group_data) {
                    $invDets[] = [
                        'id' => uniqid(),
                        'invoice_id' => $invoice_id,
                        'user_id' => $id,
                        'invoice_number' => $invoice_number,
                        'actual_amount_paid' => (integer)$group_data->total,
                        'refunded_amount' => 0,
                        'walkins_id' => $walkin_id[0]->id,
                        'agency_id' => $agency_id,
                        'agency_broadcaster' => $group_data->broadcaster_id,
                        'broadcaster_id' => $group_data->broadcaster_id,

                    ];
                }

                $mpo[] = [
                    'id' => $mpo_id,
                    'campaign_id' => $camp_id[0]->id,
                    'campaign_reference' => $camp_id[0]->campaign_reference,
                    'invoice_number' => $invoice_number,
                ];

                foreach ($group_datas as $group_data) {
                    $mpoDet[] = [
                        'id' => uniqid(),
                        'mpo_id' => $mpo_id,
                        'discount' => 0,
                        'agency_id' => $agency_id,
                        'agency_broadcaster' => $group_data->broadcaster_id,
                        'broadcaster_id' => $group_data->broadcaster_id,
                    ];
                }

                $save_invoice = Utilities::switch_db('api')->table('invoices')->insert($invoice);

                $save_invoice_details = Utilities::switch_db('api')->table('invoiceDetails')->insert($invDets);

                $save_mpo = Utilities::switch_db('api')->table('mpos')->insert($mpo);

                $save_mpo_details = Utilities::switch_db('api')->table('mpoDetails')->insert($mpoDet);

                if ($save_invoice && $save_mpo && $save_invoice_details && $save_mpo_details) {
                    foreach ($query as $q) {
                        //inserting the position into the adslot_fileposition table
                        if(!empty($q->filePosition_id)){
                            $file_pos_id = uniqid();
                            $insert_position = Utilities::switch_db('api')->update("UPDATE adslot_filePositions set select_status = 1 WHERE adslot_id = '$q->adslot_id' ");
                        }
                        $get_slots = Utilities::switch_db('api')->select("SELECT * from adslots WHERE id = '$q->adslot_id'");
                        $slots_id = $get_slots[0]->id;
                        $time_difference = (integer)$get_slots[0]->time_difference;
                        $time_used = (integer)$get_slots[0]->time_used;
                        $time = (integer)$q->time;
                        $new_time_used = $time_used + $time;
                        if ($time_difference === $new_time_used) {
                            $slot_status = 1;
                        } else {
                            $slot_status = 0;
                        }
                        $update_slot = Utilities::switch_db('api')->update("UPDATE adslots SET time_used = '$new_time_used', is_available = '$slot_status' WHERE id = '$slots_id'");
                    }

                    $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$user_id' AND agency_id = '$agency_id'");
                    $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$user_id'");
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    $description = 'Campaign '.$first->campaign_name.' created successfully by '.Session::get('agency_id');
                    $ip = request()->ip();
                    $user_activity = Api::saveActivity($agency_id, $description, $ip, $user_agent);
                    Session::forget('first_step');
                    Session::flash('success', 'Campaign created successfully');
                    return redirect()->route('dashboard');

                }else{
                    $delete_invoice = Utilities::switch_db('api')->delete("DELETE from invoices where id = '$invoice_id'");
                    $delete_invoice_details = Utilities::switch_db('api')->delete("DELETE from invoiceDetails where invoice_id = '$invoice_id'");
                    $delete_mpo = Utilities::switch_db('api')->delete("DELETE from mpos where id = '$mpo_id'");
                    $delete_mpo_details = Utilities::switch_db('api')->delete("DELETE * from mpoDetails where mpo_id = '$mpo_id'");
                    Session::flash('error', 'Could not create this campaign');
                    return redirect()->back();
                }
            }else{
                $delete_pay = Utilities::switch_db('api')->delete("DELETE from payments where id = '$pay_id'");
                $delete_pay_details = Utilities::switch_db('api')->delete("DELETE from paymentDetails where payment_id = '$pay_id'");
                $delete_files = Utilities::switch_db('api')->delete("DELETE from files where campaign_id = '$campaign_id'");
                Session::flash('error', 'Could not create this campaign');
                return redirect()->back();
            }

        } else {
            $delete_camp = Utilities::switch_db('api')->delete("DELETE from campaigns where id = '$campaign_id'");
            $delete_camp_details = Utilities::switch_db('api')->delete("DELETE from campaignDetails where campaign_id = '$campaign_id'");
            Session::flash('error', 'Could not create this campaign');
            return redirect()->back();
        }

    }

    public function getDetails($id)
    {
        $agency_id = \Session::get('agency_id');
        $campaign_details = Utilities::campaignDetails($id);
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
        $summary = Utilities::campaignDetails($campaign_id);
        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where campaign_id = '$campaign_id')");
        return response()->json(['media_channel' => $media_chanel, 'summary' => $summary]);
    }

    public function mpoDetails($id)
    {
        $mpo_details = Utilities::getMpoDetails($id);

        return view('agency.mpo.mpo')->with('mpo_details', $mpo_details);
    }

    public function getMediaChannel($campaign_id)
    {
        $channel = request()->channel;
        $broadcaster_retain = request()->media_channel;

        if(!empty($broadcaster_retain)){
            $formatted_broadcaster = "'".implode("','", $broadcaster_retain)."'";
        }


        $formatted_channel = $channel ? "'".implode("','", $channel)."'" : '';
        $all_channel = [];
        $retained_channel = [];

        if($channel){
            $broadcasters = Utilities::switch_db('api')->select("SELECT * from broadcasters where channel_id IN ($formatted_channel)");
            foreach ($broadcasters as $broadcaster){
                $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where broadcaster = '$broadcaster->id' AND campaign_id = '$campaign_id'");
                $all_channel[] = [
                    'broadcaster_id' => $campaigns ? $broadcaster->id : '',
                    'broadcaster' => $campaigns ? $broadcaster->brand : '',
                    'campaign_id' => $campaign_id ? $campaign_id : '',
                ];
            }

            if(!empty($broadcaster_retain)){
                $retained_broadcasters = Utilities::switch_db('api')->select("SELECT * from broadcasters where id IN ($formatted_broadcaster)");
                foreach ($retained_broadcasters as $retained_broadcaster){
                    $campaigns_retained = Utilities::switch_db('api')->select("SELECT * from campaignDetails where broadcaster = '$retained_broadcaster->id' AND campaign_id = '$campaign_id'");
                    $retained_channel[] = [
                        'broadcaster_id' => $campaigns_retained ? $retained_broadcaster->id : '',
                        'broadcaster' => $campaigns_retained ? $retained_broadcaster->brand : '',
                        'campaign_id' => $campaigns_retained ? $campaign_id : '',
                    ];
                }
            }

            //media mix
            $media_types = request()->channel;
            $media_mix_datas = [];
            foreach ($media_types as $media_type){
                $channel = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id = '$media_type'");
                $payments = Utilities::switch_db('api')->select("SELECT SUM(amount) as amount from paymentDetails where broadcaster IN (SELECT id from broadcasters where channel_id = '$media_type') AND payment_id = (SELECT id from payments where campaign_id = '$campaign_id')");
                $total_amount = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$campaign_id'");
                if($channel[0]->channel === 'TV'){
                    $color = '#5281FE';
                }else{
                    $color = '#00C4CA';
                }
                $media_mix_datas[] = [
                    'name' => $channel[0]->channel,
                    'y' => (integer)(($payments[0]->amount / $total_amount[0]->total) * 100),
                    'color' => $color
                ];
            }

            return response()->json(['all_channel' => $all_channel, 'media_mix' => $media_mix_datas, 'retained_channel' => $retained_channel]);
        }else{
            return null;
        }
    }

    public function complianceGraph()
    {
        $all_comp_data = [];
        $date = [];
        $campaign_id = request()->campaign_id;
        $media_channels = request()->channel;
        if($media_channels){
            foreach ($media_channels as $media_channel){
                $broadcaster = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$media_channel'");
                $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where campaign_id = '$campaign_id' AND broadcaster = '$media_channel' ");
                $channel_id = $campaigns[0]->channel;
                $stack = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id = (SELECT channel_id from broadcasters where id = '$media_channel')");
                $payments = Utilities::switch_db('api')->select("SELECT amount from paymentDetails where broadcaster = '$media_channel' AND payment_id IN (SELECT id FROM payments where campaign_id = '$campaign_id')");
                if($stack[0]->channel === 'TV'){
                    $color = '#5281FE';
                }else{
                    $color = '#00C4CA';
                }
                $all_comp_data[] = [
                    'color' => $color,
                    'name' => $broadcaster[0]->brand,
                    'data' => array($payments[0]->amount),
                    'stack' => $stack[0]->channel
                ];

            }

            $date_compliances = Utilities::switch_db('api')->select("SELECT time_created from campaignDetails where campaign_id = '$campaign_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m-%d') ");
            foreach ($date_compliances as $date_compliance){
                $date[] = [date('Y-m-d', strtotime($date_compliance->time_created))];
            }

            return response()->json(['data' => $all_comp_data, 'date' => $date]);
        }else{
            return null;
        }

    }

    public function complianceFilter()
    {
        $all_comp_data = [];
        $campaign_id = request()->campaign_id;
        $start_date = date('Y-m-d', strtotime(request()->start_date));
        $stop_date = date('Y-m-d', strtotime(request()->stop_date));
        $media_channel = request()->media_channel;
        if($media_channel){
            $broadcaster = "'".implode("','", $media_channel)."'";
        }
        $date = [];

        $complince_report_queries = Utilities::switch_db('api')->select("SELECT SUM(amount_spent) as amount_spent, broadcaster_id, channel, time_created from compliances where campaign_id = '$campaign_id' AND time_created BETWEEN '$start_date' AND '$stop_date' AND broadcaster_id IN ($broadcaster) GROUP BY broadcaster_id ");
        foreach ($complince_report_queries as $complince_report_query){
            $broadcaster = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$complince_report_query->broadcaster_id'");
            $stack = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id = '$complince_report_query->channel'");
            if($stack[0]->channel === 'TV'){
                $color = '#5281FE';
            }else{
                $color = '#00C4CA';
            }
            $all_comp_data[] = [
                'color' => $color,
                'name' =>  $broadcaster[0]->brand,
                'data' => array((integer)$complince_report_query->amount_spent),
                'stack' => $stack[0]->channel
            ];

        }

        $date_compliances = Utilities::switch_db('api')->select("SELECT time_created from compliances where campaign_id = '$campaign_id' AND time_created BETWEEN '$start_date' AND '$stop_date' GROUP BY DATE_FORMAT(time_created, '%Y-%m-%d') ");
        foreach ($date_compliances as $date_compliance){
            $date[] = [date('Y-m-d', strtotime($date_compliance->time_created))];
        }

        //media mix
        $media_mix_datas = [];
        $media_mixes = Utilities::switch_db('api')->select("SELECT SUM(amount_spent) as total_amount_spent, channel FROM compliances where campaign_id = '$campaign_id' AND time_created BETWEEN '$start_date' AND '$stop_date' GROUP BY channel");
        $total_amount = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$campaign_id'");
        foreach ($media_mixes as $media_mix){
            $channel = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id = '$media_mix->channel'");
            if($channel[0]->channel === 'TV'){
                $color = '#5281FE';
            }else{
                $color = '#00C4CA';
            }
            $media_mix_datas[] = [
                'name' => $channel[0]->channel,
                'y' => (integer)(($media_mix->total_amount_spent / $total_amount[0]->total) * 100),
                'color' => $color
            ];
        }


        return response()->json(['date' => $date, 'data' => $all_comp_data, 'media_mix' => $media_mix_datas]);
    }

    public function updateBudget(Request $request)
    {
        Session::get('first_step')->campaign_budget = $request->campaign_budget;
        Session::flash('success', 'Campaign Budget Updated');
        return back();
    }


}