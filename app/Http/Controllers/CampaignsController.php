<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Cloudinary;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Maths;
use Vanguard\Libraries\Utilities;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Session;


class CampaignsController extends Controller
{
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
        $agency_campaigns = [];
        $broadcaster_id = Session::get('broadcaster_id');
        $today_date = date("Y-m-d");

            if($request->has('start_date') && $request->has('stop_date')) {
                $start_date = $request->start_date;
                $stop_date = $request->stop_date;
                $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, p.total, b.name as brand_name, c.campaign_reference from campaignDetails as c_d, payments as p, campaigns as c, brands as b where c.id = c_d.campaign_id and p.campaign_id = c_d.campaign_id and c_d.brand = b.id and c_d.broadcaster = '$broadcaster_id' and c_d.start_date <= '$today_date' and c_d.stop_date > '$today_date' and c_d.stop_date > '$start_date' and c_d.stop_date > '$stop_date' and c_d.adslots  > 0 ORDER BY c_d.time_created DESC");
            }else {
                $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.adslots_id, c_d.stop_date, c_d.start_date, c_d.time_created, c_d.product, c_d.name, c_d.campaign_id, p.total, b.name as brand_name, c.campaign_reference from campaignDetails as c_d, payments as p, campaigns as c, brands as b where c.id = c_d.campaign_id and p.campaign_id = c_d.campaign_id and c_d.brand = b.id and c_d.broadcaster = '$broadcaster_id' and c_d.start_date <= '$today_date' and c_d.stop_date > '$today_date' and c_d.adslots  > 0 ORDER BY c_d.time_created DESC");
            }

            $j = 1;
            foreach ($all_campaigns as $cam)
            {

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
                $agency_campaigns[] = [
                    'id' => $cam->campaign_reference,
                    'camp_id' => $cam->campaign_id,
                    'name' => $cam->name,
                    'brand' => ucfirst($cam->brand_name),
                    'product' => $cam->product,
                    'date_created' => date('M j, Y', strtotime($cam->time_created)),
                    'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                    'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                    'adslots' => count((explode(',', $cam->adslots_id))),
                    'budget' => number_format($cam->total, 2),
                    'status' => $status
                ];
                $j++;
            }

            return $dataTables->collection($agency_campaigns)
                ->addColumn('name', function ($agency_campaigns) {
                    return '<a href="'.route('broadcaster.campaign.details', ['id' => $agency_campaigns['camp_id']]).'">'.$agency_campaigns['name'].'</a>';
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
    public function create()
    {
        return view('campaign.create1');
    }

    public function createStep2($walkins)
    {

        $walkins_id = Utilities::switch_db('api')->select("SELECT id from walkIns where user_id = '$walkins'");
        $walk_id = $walkins_id[0]->id;
        $industry = Utilities::switch_db('api')->select("SELECT * from sectors");
        $sub_industries = Utilities::switch_db('api')->select("SELECT * from subSectors");
        $chanel = Utilities::switch_db('api')->select("SELECT * from campaignChannels");
        $brands = Utilities::switch_db('api')->select("SELECT * from brands WHERE walkin_id = '$walk_id'");
        $target_audience = Utilities::switch_db('api')->select("SELECT * from targetAudiences");
        $day_parts = Utilities::switch_db('api')->select("SELECT * from dayParts");
        $regions = Utilities::switch_db('api')->select("SELECT * from regions");

        Api::validateCampaign();

        return view('campaign.create2')->with('day_parts', $day_parts)
                                            ->with('step2', Session::get('step2'))
                                            ->with('target_audience', $target_audience)
                                            ->with('industry', $industry)
                                            ->with('chanel', $chanel)
                                            ->with('regions', $regions)
                                            ->with('walkins_id', $walkins)
                                            ->with('brands', $brands);

    }

    public function getIndustrySubIndustry()
    {
        $brand_id = request()->brand;
        $brand = Utilities::switch_db('api')->select("SELECT * from brands where id = '$brand_id'");
        $industry_id = $brand[0]->industry_id;
        $sub_industry_id = $brand[0]->sub_industry_id;
        $industry = Utilities::switch_db('api')->select("SELECT * from sectors where sector_code = '$industry_id'");
        $sub_industry = Utilities::switch_db('api')->select("SELECT * from subSectors where sub_sector_code = '$sub_industry_id'");
        if($industry && $sub_industry){
            return response()->json(['industry' => $industry, 'sub_industry' => $sub_industry]);
        }else{
            return response()->json(['error' => 'error']);
        }
    }

    public function postStep2(Request $request, $walkins)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $this->validate($request, [
            'name' => 'required',
            'brand' => 'required',
            'product' => 'required',
            'channel' => 'required',
            'min_age' => 'required',
            'max_age' => 'required',
            'target_audience' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'industry' => 'required',
            'dayparts' => 'required_without_all',
            'region' => 'required',
        ]);

        if($request->min_age < 0 || $request->max_age < 0){
            Session::flash('error', 'The minimum or maximum age cannot have a negetive value');
            return back();
        }

        if($request->min_age > $request->max_age){
            Session::flash('error', 'The minimum age cannot be greater than the maximum age');
            return back();
        }

        if(strtotime($request->end_date) < strtotime($request->start_date)){
            return redirect()->back()->with('error', 'Start Date cannot be greater than End Date');
        }

        $step2_req = ((object) $request->all());
        session(['step2' => $step2_req]);

        $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$walkins' AND broadcaster_id = '$broadcaster_id'");
        $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$walkins'");
        $del_file_position = Utilities::switch_db('api')->delete("DELETE FROM adslot_filePositions where select_status = 0");

        return redirect()->route('campaign.create3', ['walkins' => $walkins])->with('walkins', $walkins);
    }

    public function createStep3($walkins)
    {
        $broadcaster = \Session::get('broadcaster_id');
        $step1 = Session::get('step2');
        if(!$step1){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }
        $day_parts = implode("','" ,$step1->dayparts);
        $region = implode("','", $step1->region);
        $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience = '$step1->target_audience' AND day_parts IN ('$day_parts') AND region IN ('$region') AND is_available = 0 AND broadcaster = '$broadcaster' AND channels='$step1->channel' group by broadcaster");

        $ads_broad = [];
        foreach ($adslots as $adslot)
        {
            $broad = Utilities::switch_db('api')->select("SELECT brand from broadcasters where id = '$adslot->broadcaster'");
            $ads_broad[] = [
                'broadcaster' => $adslot->broadcaster,
                'count_adslot' => $adslot->all_slots,
                'boradcaster_brand' => $broad[0]->brand,
            ];
        }

        return view('campaign.create3', ['walkins' => $walkins])->with('adslots', $ads_broad)
            ->with('walkins', $walkins);
    }

    public function postStep3(Request $request, $walkins)
    {

        return redirect()->route('campaign.create4', ['walkins' => $walkins]);
    }

    public function createStep4($walkins)
    {
        $broadcaster = \Session::get('broadcaster_id');
        $delete_uploads_without_files = \DB::delete("DELETE from uploads where user_id = '$walkins' AND time = 0");


        return view('campaign.create4', ['walkins' => $walkins])->with('walkins', $walkins)
                            ->with('broadcaster', $broadcaster);
    }

    public function postStep4(Request $request, $walkins)
    {
        $broadcaster = Session::get('broadcaster_id');

        if(((int)$request->f_du) > ((int)$request->time)){
            Session::flash('error', 'Your video file duration cannot be more than the time slot you picked');
            return redirect()->back();
        }

        $check_file = \DB::select("SELECT * from uploads where user_id = '$walkins'");
        if(count($check_file) > 4){
            Session::flash('error', 'You cannot upload more than 4 files');
            return redirect()->back();
        }

        if ($request->hasFile('uploads')) {

            $this->validate($request, [
                'uploads' => 'required|max:20000',
                'time' => 'required'
            ]);

            $filesUploaded = $request->uploads;
            $extension = $filesUploaded->getClientOriginalExtension();
            if($extension == 'mp4' || $extension == 'wma' || $extension == 'ogg' || $extension == 'mkv'){

                $time = $request->time;
                $uploads = \DB::select("SELECT * from uploads where user_id = '$walkins' AND time = '$time'");
                if(count($uploads) === 1){
                    Session::flash('error', 'You cannot upload twice for this time slot');
                    return back();
                }

                $filename = realpath($filesUploaded);
                Cloudder::uploadVideo($filename);
                $clouder = Cloudder::getResult();
                $file_gan_gan = encrypt($clouder['url']);

                $insert_upload = \DB::table('uploads')->insert([
                    'user_id' => $walkins,
                    'time' => $time,
                    'uploads' => $file_gan_gan
                ]);

                if($insert_upload){
                    $msg = 'Your upload for '.$time.' seconds was successful';
                    Session::flash('success', $msg);
                    return redirect()->route('campaign.create4', ['walkins' => $walkins]);
                }else{
                    Session::flash('error', 'Could not complete upload process');
                    return back();
                }
            }

        }else{
            Session::flash('error', 'Please choose a file');
            return back();
        }
    }

    public function removeMedia($walkins, $id)
    {
        $media_file = \DB::select("SELECT * from uploads where id = '$id' AND user_id = '$walkins'");
        $public_id = $media_file[0]->file_code;

        $c = Cloudder::destroy($public_id, array("invalidate" => TRUE, 'resource_type' => 'video', 'type' => 'upload'));

        $deleteUploads = \DB::delete("DELETE from uploads WHERE id = '$id' AND user_id = '$walkins'");
        if($deleteUploads){
            Session::flash('success', 'File deleted successfully...');
            return back();
        }else{
            Session::flash('error', 'Error deleting file...');
            return back();
        }
    }

    public function postStep4_1(Request $request, $walkins)
    {
        $broadcaster = Session::get('broadcaster_id');
        $get_uploaded_files = \DB::select("SELECT * from uploads where user_id = '$walkins'");
        $count_files = count($get_uploaded_files);
        if($count_files === 0){
            Session::flash('error', 'You have not uploaded any file(s)');
            return redirect()->back();
        }else{
            $remaining_file = 4 - $count_files;
            for ($i = 0; $i < $remaining_file; $i++){
                $insert_upload = \DB::table('uploads')->insert([
                    'user_id' => $walkins,
                    'time' => 00,
                ]);
            }

            return redirect()->route('campaign.create6', ['walkins' => $walkins]);
        }
    }

    public function createStep6($walkins)
    {
        $broadcaster = \Session::get('broadcaster_id');
        $step1 = Session::get('step2');
        if(!$step1){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }
        $day_parts = "". implode("','" ,$step1->dayparts) . "";
        $region = "". implode("','", $step1->region) ."";
        $adslots = Utilities::switch_db('api')->select("SELECT * FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND channels='$step1->channel' AND target_audience = '$step1->target_audience' AND day_parts IN ('$day_parts') AND region IN ('$region') AND is_available = 0 AND broadcaster = '$broadcaster'");
        $result = count($adslots);
        return view('campaign.create6')->with('results', $result)->with('walkins', $walkins);
    }


    public function createStep8($id)
    {
        return view('campaign.create8');
    }

    public function createStep9($id)
    {
        return view('campaign.create9');
    }

    public function getStep7($walkins)
    {
        $rate_card = [];
        $broadcaster = \Session::get('broadcaster_id');
        $step1 = Session::get('step2');
        if(!$step1){
            Session::flash('error', 'Data lost, please go back and select your filter criteria');
            return back();
        }
        $day_parts = "". implode("','" ,$step1->dayparts) . "";
        $region = "". implode("','", $step1->region) ."";
        $adslots_count = Utilities::switch_db('api')->select("SELECT * FROM adslots where min_age >= $step1->min_age AND channels='$step1->channel' AND  max_age <= $step1->max_age AND target_audience = '$step1->target_audience' AND day_parts IN ('$day_parts') AND region IN ('$region') AND is_available = 0 AND broadcaster = '$broadcaster'");
        $result = count($adslots_count);
        $ratecards = Utilities::switch_db('api')->select("SELECT * from rateCards WHERE id IN (SELECT rate_card FROM adslots where min_age >= $step1->min_age 
                                                            AND max_age <= $step1->max_age 
                                                            AND target_audience = '$step1->target_audience' 
                                                            AND day_parts IN ('$day_parts') AND region IN ('$region') 
                                                            AND is_available = 0 AND broadcaster = '$broadcaster')");

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

        $data = \DB::select("SELECT * from uploads WHERE user_id = '$walkins'");
        $cart = \DB::select("SELECT * from carts WHERE user_id = '$walkins'");
        $broadcaster_logo = Utilities::switch_db('api')->select("SELECT image_url from broadcasters where id = '$broadcaster'");

        $positions = Utilities::switch_db('api')->select("SELECT * from filePositions where broadcaster_id = '$broadcaster'");

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($rate_card);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('/campaign/create/'.$walkins.'/step7/get');

        return view('campaign.create7')->with('ratecards', $entries)->with('result', $result)->with('cart', $cart)->with('datas', $data)->with('times', $time)->with('walkins', $walkins)->with('broadcaster_logo', $broadcaster_logo)->with('positions', $positions);
    }

    /**
     * @param Request $request
     * @return string
     */
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
        $broadcaster = Session::get('broadcaster_id');
        $adslot_id = $request->adslot_id;
        $position = $request->position;
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

        $check = \DB::select("SELECT * from carts where adslot_id = '$adslot_id' and user_id = '$user' and filePosition_id = '$position' and filePosition_id != ''");
        if(count($check) === 1){
            return response()->json(['error' => 'error']);
        }


        $insert = \DB::insert("INSERT INTO carts (user_id, broadcaster_id, price, ip_address, file, from_to_time, `time`, adslot_id, percentage, total_price, filePosition_id, status) VALUES ('$user','$broadcaster','$price','$ip','$file','$hourly_range','$time','$adslot_id', '$percentage', '$new_price', '$position', 1)");
        if($insert){
            return response()->json(['success' => 'success']);
        }else{
            return response()->json(['failure' => 'failure']);
        }
    }

    public function getCheckout($walkins)
    {
        $query = [];
        $broadcaster = Session::get('broadcaster_id');
        $first = Session::get('step2');
        $day_parts = "". implode("','" ,$first->dayparts) . "";
        $region = "". implode("','", $first->region) ."";
        $brands = Utilities::switch_db('api')->select("SELECT name from brands where id = '$first->brand'");
        $day_partss = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id IN ('$day_parts') ");
        $targets = Utilities::switch_db('api')->select("SELECT audience from targetAudiences where id = '$first->target_audience'");
        $regions = Utilities::switch_db('api')->select("SELECT region from regions where id IN ('$region') ");
        $calc = \DB::select("SELECT SUM(total_price) as total_price FROM carts WHERE user_id = '$walkins' AND broadcaster_id = '$broadcaster'");
        $query_carts = \DB::select("SELECT * FROM carts WHERE user_id = '$walkins' AND broadcaster_id = '$broadcaster'");
        $user = Utilities::switch_db('api')->select("SELECT * from users where id = '$walkins' ");
        foreach ($query_carts as $query_cart){
            $position = Utilities::switch_db('api')->select("SELECT * from filePositions where id = '$query_cart->filePosition_id'");
            $query[] = [
              'id' => $query_cart->id,
              'from_to_time' => $query_cart->from_to_time,
              'time' => $query_cart->time,
              'price' => $query_cart->price,
              'percentage' => $query_cart->percentage,
              'position' => $position ? $position[0]->position : 'No Position',
              'total_price' => $query_cart->total_price
            ];
        }

        return view('campaign.create9')->with('first_session', $first)
            ->with('calc', $calc)
            ->with('day_part', $day_partss)
            ->with('region', $regions)
            ->with('target', $targets)
            ->with('query', $query)
            ->with('brand', $brands)
            ->with('broadcaster', $broadcaster)
            ->with('walkins', $walkins)
            ->with('user', $user);
    }

    Public function postCampaign(Request $request, $walkins)
    {

        $save_campaign = $this->saveCampaign($request, $walkins);
        if($save_campaign === 'success'){
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $description = 'Campaign created by '.Session::get('broadcaster_id').' for '.$walkins;
            $ip = request()->ip();
            $user_activity = Api::saveActivity($walkins, $description, $ip, $user_agent);
            Session::flash('success', 'Campaign created successfully');
            return redirect()->route('campaign.all');
        }else{
            Session::flash('error', 'There was problem creating campaign');
            return redirect()->back();
        }

    }

    public function removeCart($id)
    {
        $rate_id = $id;
        $del = \DB::select("DELETE FROM carts WHERE rate_id = '$rate_id'");
        return redirect()->back()->with('success', trans('app.campaign'));
    }

    public function payCampaign(Request $request)
    {

        $insert = [
            'id' => uniqid(),
            'user_id' => $request->user_id,
            'reference' => $request->reference,
            'amount' => $request->amount,
            'status' => 'PENDING',
        ];

        $req = [];
        $user_id = $request->user_id;

        $transaction = Utilities::switch_db('api')->table('transactions')->insert($insert);

        $response = $this->query_api_transaction_verify($request->reference);

        if($response['status'] === true){

            $amount = ($response['data']['amount']/100);
            $card = $response['data']['authorization']['card_type'];
            $status = $response['data']['status'];
            $message = $response['message'];
            $reference = $response['data']['reference'];
            $ip_address = $response['data']['ip_address'];
            $fees = $response['data']['fees'];
            $user_id = $request->user_id;
            $type = 'FUND WALLET';

            $update_transaction = Utilities::switch_db('api')->select("UPDATE transactions SET card_type = '$card', status = 'SUCCESSFUL', ip_address = '$ip_address', fees = '$fees', `type` = '$type', message = '$message' WHERE reference = '$reference'");

            if ($transaction) {
                $req = [
                    'payment' => 'card',
                    'total' => $amount,
                ];
                $request = (object)$req;
                $save_campaign = $this->saveCampaign($request, $user_id);;
                if($save_campaign === 'success'){
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    $description = 'Payment of '.$amount.' to '.Session::get('broadcaster_id').' by '.$user_id.' For Campaign';
                    $ip = request()->ip();
                    $user_activity = Api::saveActivity($user_id, $description, $ip, $user_agent);

                    $msg = 'Your payment of '. $amount.' is successful and campaign has been created ';
                    Session::flash('success', $msg);
                    return redirect()->route('campaign.all');
                }else{
                    Session::flash('error', 'There was problem creating campaign');
                    return redirect()->back();
                }

            }

        } else {
            Session::flash('error', 'Sorry, something went wrong! Please contact the Administrator or Bank.');
            return redirect()->back();
        }


    }

    protected function query_api_transaction_verify($reference)
    {
        $result = array();
        $url = 'https://api.paystack.co/transaction/verify/'.$reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer sk_test_485de9008374bbad12f121fefe3afe01d1568fbd']
        );
        $request = curl_exec($ch);
        curl_close($ch);

        if ($request) {
            $result = json_decode($request, true);
            return $result;
        } else {
            return false;
        }
    }

    public function saveCampaign($request, $walkins)
    {
//        dd($request);
        $broadcaster = Session::get('broadcaster_id');
        $first = Session::get('step2');
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$walkins' AND broadcaster_id = '$broadcaster'");
        $ads = [];

        foreach ($query as $q)
        {
            $ads[] = $q->adslot_id;
        }
        $data = \DB::select("SELECT * from uploads WHERE user_id = '$walkins'");

        $new_q = [];
        $pay = [];
        $payDetails = [];
        $campDetails = [];
        $camp = [];
        $invoice = [];
        $mpo = [];
        $mpoDetails = [];
        $invoiceDetails = [];
        $file_position = [];
        $i = 0;

        $adssss = implode(',' ,$ads);
        $campaign_id = uniqid();
        $pay_id = uniqid();
        $invoice_id = uniqid();
        $mpo_id = uniqid();
        $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns where user_id = '$walkins'");
        $now = strtotime(Carbon::now('Africa/Lagos'));

        $campaign_reference = Utilities::generateReference();
        $invoice_number = Utilities::generateReference();

//        storing campaign
        $camp[] = [
            'id' => $campaign_id,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'campaign_reference' => $campaign_reference
        ];

        $calc = \DB::select("SELECT SUM(total_price) as total_price FROM carts WHERE user_id = '$walkins' GROUP BY broadcaster_id");

        $campDetails[] = [
            'id' => uniqid(),
            'campaign_id' => $campaign_id,
            'user_id' => $walkins,
            'broadcaster' => $broadcaster,
            'channel' => $first->channel,
            'brand' => $first->brand,
            'start_date' => date('Y-m-d', strtotime($first->start_date)),
            'stop_date' => date('Y-m-d', strtotime($first->end_date)),
            'name' => $first->name,
            'product' => $first->product,
            'day_parts' => "'". implode("','" ,$first->dayparts) . "'",
            'target_audience' => $first->target_audience,
            'region' => implode(',' ,$first->region),
            'min_age' => (integer)$first->min_age,
            'max_age' => (integer)$first->max_age,
            'industry' => $first->industry,
            'adslots' => count($query),
            'walkins_id' => $walkin_id[0]->id,
            'agency' => $walkin_id[0]->id,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'adslots_id' => "'". implode("','" ,$ads) . "'",
        ];

        $check_time_adslots = Utilities::fetchTimeInCart($walkins, $broadcaster);
        foreach ($check_time_adslots as $check_time_adslot){
            if($check_time_adslot['initial_time_left'] < $check_time_adslot['time_bought']){
                $msg = 'You cannot proceed with the campaign creation because '.$check_time_adslot['from_to_time'].' for '.$check_time_adslot['broadcaster_name'].' isn`t available again';
                \Session::flash('info', $msg);
                return back();
            }
        }

        $save_campaignDetails = Utilities::switch_db('api')->table('campaignDetails')->insert($campDetails);
        $save_campaign = Utilities::switch_db('api')->table('campaigns')->insert($camp);

        if($save_campaign && $save_campaignDetails){
            $camp_id = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE id='$campaign_id'");
            foreach($query as $q)
            {

                $new_q[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'file_name' => $q->file,
                    'broadcaster_id' => $broadcaster,
                    'file_url' => $q->file,
                    'adslot' => $q->adslot_id,
                    'user_id' => $walkins,
                    'file_code' => Utilities::generateReference(),
                    'time_created' => date('Y-m-d H:i:s', $now),
                    'time_modified' => date('Y-m-d H:i:s', $now),
                    'time_picked' => $q->time,
                    'position_id' => $q->filePosition_id,
                ];
            }

            $pay[] = [
                'id' => $pay_id,
                'campaign_id' => $camp_id[0]->id,
                'campaign_reference' => $camp_id[0]->campaign_reference,
                'total' => $request->total,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
            ];

            $payDetails[] = [
                'id' => uniqid(),
                'payment_id' => $pay_id,
                'payment_method' => $request->payment,
                'amount' => (integer) $calc[0]->total_price,
                'broadcaster' => $broadcaster,
                'walkins_id' => $walkin_id[0]->id,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
            ];

            $save_payment = Utilities::switch_db('api')->table('payments')->insert($pay);

            $save_payment_details = Utilities::switch_db('api')->table('paymentDetails')->insert($payDetails);

            $save_file = Utilities::switch_db('api')->table('files')->insert($new_q);

            if($save_payment && $save_file && $save_payment_details){

                $payment_id = Utilities::switch_db('api')->select("SELECT id from payments WHERE id='$pay_id'");

                $invoice[] = [
                    'id' => $invoice_id,
                    'campaign_id' => $camp_id[0]->id,
                    'campaign_reference' => $camp_id[0]->campaign_reference,
                    'invoice_number' => $invoice_number,
                    'payment_id' => $payment_id[0]->id,
                ];

                $invoiceDetails[] = [
                    'id' => uniqid(),
                    'invoice_id' => $invoice_id,
                    'user_id' => $walkins,
                    'broadcaster_id' => $broadcaster,
                    'invoice_number' => $invoice_number,
                    'actual_amount_paid' => (integer) $calc[0]->total_price,
                    'refunded_amount' => 0,
                    'walkins_id' => $walkin_id[0]->id,
                ];

                $mpo[] = [
                    'id' => $mpo_id,
                    'campaign_id' => $camp_id[0]->id,
                    'campaign_reference' => $camp_id[0]->campaign_reference,
                    'invoice_number' => $invoice_number,
                ];

                $mpoDetails[] = [
                    'id' => uniqid(),
                    'mpo_id' => $mpo_id,
                    'broadcaster_id' => $broadcaster,
                    'discount' => 0,
                ];

                $save_invoice = Utilities::switch_db('api')->table('invoices')->insert($invoice);

                $save_invoice_details = Utilities::switch_db('api')->table('invoiceDetails')->insert($invoiceDetails);

                $save_mpo = Utilities::switch_db('api')->table('mpos')->insert($mpo);

                $save_mpo_details = Utilities::switch_db('api')->table('mpoDetails')->insert($mpoDetails);

                if($save_invoice && $save_mpo && $save_invoice_details && $save_mpo_details){
                    foreach ($query as $q){
                        //inserting the position into the adslot_fileposition table
                        if(!empty($q->filePosition_id)){
                            $file_pos_id = uniqid();
                            $insert_position = Utilities::switch_db('api')->update("UPDATE adslot_filePositions set select_status = 1 WHERE adslot_id = '$q->adslot_id' AND broadcaster_id = '$broadcaster'");
                        }
                        $get_slots = Utilities::switch_db('api')->select("SELECT * from adslots WHERE id = '$q->adslot_id'");
                        $slots_id = $get_slots[0]->id;
                        $time_difference = $get_slots[0]->time_difference;
                        $time_used = $get_slots[0]->time_used;
                        $time = $q->time;
                        $new_time_used = $time_used + $time;
                        if($time_difference === $new_time_used){
                            $slot_status = 1;
                        }else{
                            $slot_status = 0;
                        }
                        $update_slot = Utilities::switch_db('api')->update("UPDATE adslots SET time_used = '$new_time_used', is_available = '$slot_status' WHERE id = '$slots_id'");
                    }

                    $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$walkins'");
                    $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$walkins'");
                    Session::forget('step2');
                    return 'success';
                }else{
                    $delete_invoice = Utilities::switch_db('api')->delete("DELETE from invoices where id = '$invoice_id'");
                    $delete_invoice_details = Utilities::switch_db('api')->delete("DELETE from invoiceDetails where invoice_id = '$invoice_id'");
                    $delete_mpo = Utilities::switch_db('api')->delete("DELETE from mpos where id = '$mpo_id'");
                    $delete_mpo_details = Utilities::switch_db('api')->delete("DELETE * from mpoDetails where mpo_id = '$mpo_id'");
                    return 'error';
                }
            }else{
                $delete_pay = Utilities::switch_db('api')->delete("DELETE from payments where id = '$pay_id'");
                $delete_pay_details = Utilities::switch_db('api')->delete("DELETE from paymentDetails where payment_id = '$pay_id'");
                $delete_files = Utilities::switch_db('api')->delete("DELETE from files where campaign_id = '$campaign_id'");
                return 'error';
            }

        }else{

            return 'error';
        }

    }

    public function campaignDetails($id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $campaign_details = Utilities::campaignDetails($id);
        $user_id = $campaign_details['campaign_det']['company_user_id'];
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where broadcaster = '$broadcaster_id' and user_id = '$user_id' ");
        $all_clients = Utilities::switch_db('api')->select("SELECT * FROM walkIns where broadcaster_id = '$broadcaster_id'");
        return view('broadcaster_module.campaigns.details', compact('campaign_details', 'all_campaigns', 'all_clients'));
    }

    public function filterByUser($user_id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $all_campaigns = Utilities::switch_db('api')->select("SELECT * FROM campaignDetails where broadcaster = '$broadcaster_id' and user_id = '$user_id' ");

        $media_chanel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id = '$broadcaster_id'");
        return (['campaign' => $all_campaigns, 'channel' => $media_chanel]);
    }

    public function getMediaChannel($campaign_id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $channel = request()->channel;
        $broadcaster_retain = request()->media_channel;

        if(!empty($broadcaster_retain)){
            $formatted_broadcaster = "'".implode("','", $broadcaster_retain)."'";
        }


        $formatted_channel = $channel ? "'".implode("','", $channel)."'" : '';
        $all_channel = [];
        $retained_channel = [];

        if($channel){
            $broadcasters = Utilities::switch_db('api')->select("SELECT * from broadcasters where channel_id IN ($formatted_channel) and id = '$broadcaster_id'");
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
        /**
         * The compliance graph
         *The first graph you see when you select media types and subsequest media channels attached on the campaign details page is just a summary of how the campaign budget is been spent on the different
         *broadcasters and are grouped by the media types they belong to, in my case (TV, Radio for now)
         *
         * When the filter is then applied, it hits this method to fetch data from the compliances table.
         */
        $broadcaster_id = Session::get('broadcaster_id');
        $all_comp_data = [];
        $compliance_datas = [];
        $campaign_id = request()->campaign_id;
        $start_date = date('Y-m-d', strtotime(request()->start_date));
        $stop_date = date('Y-m-d', strtotime(request()->stop_date));
        $media_channel = request()->media_channel;
        if($media_channel){
            $broadcaster = "'".implode("','", $media_channel)."'";
        }


        $dates = [];

        //querying the compliances table get date
        $date_compliances = Utilities::switch_db('api')->select("SELECT time_created from compliances where campaign_id = '$campaign_id' AND time_created BETWEEN '$start_date' AND '$stop_date' and broadcaster_id = '$broadcaster_id' GROUP BY DATE_FORMAT(time_created, '%Y-%m-%d') ");
        foreach ($date_compliances as $date_compliance){
            $date_created = date('Y-m-d', strtotime($date_compliance->time_created));
            //this query results to a multidimensional array
            $compliances = Utilities::switch_db('api')->select("SELECT IF(c.amount_spent IS NOT NULL, sum(c.amount_spent), 0) as amount, c.broadcaster_id, c.campaign_id, date_format(c.time_created, '%Y-%m-%d') as `time`, b.brand, e.channel as stack, c.channel from compliances as c, broadcasters as b, campaignChannels as e where c.broadcaster_id = '$broadcaster_id' and b.id = '$broadcaster_id' and c.channel = e.id and c.broadcaster_id = '$broadcaster_id' and c.campaign_id = '$campaign_id' and date_format(c.time_created, '%Y-%m-%d') = '$date_created' ");

            $all_comp_data[] = $compliances;
            $dates[] = [date('Y-m-d', strtotime($date_compliance->time_created))];
        }

        //array_flatten brings out all the arrays to form an array of objects
        $flatened_comp = array_flatten($all_comp_data);

        //returned back to array of arrays
        $array = json_decode(json_encode($flatened_comp), true);

        $final_arr = array();

        //this group the array by broadcasters
        foreach($array as $key=>$value){

            if(!array_key_exists($value['broadcaster_id'],$final_arr)){
                $final_arr[$value['broadcaster_id']] = $value;
                unset($final_arr[$value['broadcaster_id']]['amount']);
                $final_arr[$value['broadcaster_id']]['time_amount'] =array();
                $final_arr[$value['broadcaster_id']]['amount'] =array();

                array_push($final_arr[$value['broadcaster_id']]['time_amount'],$value['time']);
                array_push($final_arr[$value['broadcaster_id']]['amount'],(integer)$value['amount']);
            }else
            {
                array_push($final_arr[$value['broadcaster_id']]['time_amount'],$value['time']);
                array_push($final_arr[$value['broadcaster_id']]['amount'],(integer)$value['amount']);
            }
        }

        $array_values = array_values($final_arr);

        foreach ($array_values as $array_value){
            if($array_value['stack'] === 'TV'){
                $color = '#5281FE';
            }else{
                $color = '#00C4CA';
            }
            $compliance_datas[] = [
                'color' => $color,
                'name' => $array_value['brand'],
                'data' => $array_value['amount'],
                'stack' => $array_value['stack']
            ];
        }

        //media mix
        $media_mix_datas = [];
        $media_mixes = Utilities::switch_db('api')->select("SELECT SUM(amount_spent) as total_amount_spent, channel FROM compliances where campaign_id = '$campaign_id' AND time_created BETWEEN '$start_date' AND '$stop_date' AND broadcaster_id IN ($broadcaster) GROUP BY channel");
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


        return response()->json(['date' => $dates, 'data' => $compliance_datas, 'media_mix' => $media_mix_datas]);
    }


}
