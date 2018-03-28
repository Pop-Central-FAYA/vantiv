<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('campaign.index');
    }

    public function getAllData(Datatables $datatables, Request $request)
    {
        $campaign = [];
        $j = 1;
        $broadcaster = Session::get('broadcaster_id');
        $broadcaster_user = Session::get('broadcaster_user_id');
        if($broadcaster){
            $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE broadcaster = '$broadcaster' AND adslots > 0 ORDER BY time_created desc");
        }else{
            $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE agency_broadcaster = '$broadcaster_user' AND adslots > 0 ORDER BY time_created desc");
        }

        foreach ($all_campaign as $cam)
        {
//            $today = strtotime(date('Y-m-d'));
            $today = date("Y-m-d");
            if(strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)){
                $status = 'Campaign Expired';
            }elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)){
                $status = 'Campaign In Progress';
            }else{
                $now = strtotime($today);
                $your_date = strtotime($cam->start_date);
                $datediff = $your_date - $now;
                $new_day =  round($datediff / (60 * 60 * 24));
                $status = 'Campaign to start in '.$new_day.' day(s)';
            }
            $brand = Utilities::switch_db('api')->select("SELECT `name` as brand_name from brands where id = '$cam->brand'");
            $campaign[] = [
                'id' => $j,
                'camp_id' => $cam->id,
                'name' => $cam->name,
                'brand' => $brand[0]->brand_name,
                'product' => $cam->product,
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'adslots' => $cam->adslots,
                'compliance' => '0%',
                'status' => $status
            ];
            $j++;
        }

        return $datatables->collection($campaign)
            ->addColumn('details', function ($campaign) {
                return '<a href="' . route('broadcaster.campaign.details', ['id' => $campaign['camp_id']]) .'" class="btn btn-primary btn-xs" > Campaign Details </a>';
            })
            ->rawColumns(['details' => 'details'])->addIndexColumn()
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
        $industry = Utilities::switch_db('api')->select("SELECT id, `name` from sectors");
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

    public function postStep2(Request $request, $walkins)
    {
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

        $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$walkins'");
        $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$walkins'");

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
                    return redirect()->route('campaign.create4', ['walkins' => $walkins]);
                }else{
                    Session::flash('error', 'Could not complete upload process');
                    return back();
                }
            }

        }
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
        return view('campaign.create7')->with('ratecards', $rate_card)->with('result', $result)->with('cart', $cart)->with('datas', $data)->with('times', $time)->with('walkins', $walkins)->with('broadcaster_logo', $broadcaster_logo);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function postCart(Request $request)
    {
        $this->validate($request, [
            'price' => 'required',
            'file' => 'required',
            'time' => 'required',
            'adslot_id' => 'required|unique:carts',
        ]);
        $price = $request->price;
        $file = $request->file;
        $time = $request->time;
        $id = $request->adslot_id;
        $adslot = Utilities::switch_db('api')->select("SELECT * FROM adslots where id = '$id'");
        $time_difference = $adslot[0]->time_difference;
        $time_used = $adslot[0]->time_used;
        $total_time = $time_used + $time;
        if($total_time > $time_difference){
            Session::flash('error', 'This file duration cannot fit in the slot, please pick another one');
            return back();
        }
        $hourly_range = $request->range;
        $user = $request->walkins;
        $broadcaster = Session::get('broadcaster_id');
        $adslot_id = $request->adslot_id;
        $ip = \Request::ip();
        $insert = \DB::insert("INSERT INTO carts (user_id, broadcaster_id, price, ip_address, file, from_to_time, `time`, adslot_id) VALUES ('$user','$broadcaster','$price','$ip','$file','$hourly_range','$time','$adslot_id')");
        if($insert){
            return "success";
        }else{
            return "failure";
        }
    }

    public function getCheckout($walkins)
    {
        $broadcaster = Session::get('broadcaster_id');
        $first = Session::get('step2');
        $day_parts = "". implode("','" ,$first->dayparts) . "";
        $region = "". implode("','", $first->region) ."";
        $brands = Utilities::switch_db('api')->select("SELECT name from brands where id = '$first->brand'");
        $day_partss = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id IN ('$day_parts') ");
        $targets = Utilities::switch_db('api')->select("SELECT audience from targetAudiences where id = '$first->target_audience'");
        $regions = Utilities::switch_db('api')->select("SELECT region from regions where id IN ('$region') ");
        $calc = \DB::select("SELECT SUM(price) as total_price FROM carts WHERE user_id = '$walkins'");
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$walkins'");
        $user = Utilities::switch_db('api')->select("SELECT * from users where id = '$walkins'");

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

    public function campaignDetails($id)
    {
        $campaign_details = Utilities::campaignDetails($id);
        return view('campaign.campaign_details', compact('campaign_details'));
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

                    $msg = 'Your payment of '. $amount.' is successful and campaign has been created successfully';
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
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$walkins'");
        $ads = [];

        foreach ($query as $q)
        {
            $ads[] = $q->adslot_id;
        }
        $data = \DB::select("SELECT * from uploads WHERE user_id = '$walkins'");

        $new_q = [];
        $pay = [];
        $camp = [];
        $invoice = [];
        $mpo = [];
        $i = 0;

        $adssss = implode(',' ,$ads);
        $campaign_id = uniqid();
        $pay_id = uniqid();
        $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns where user_id = '$walkins'");
        $now = strtotime(Carbon::now('Africa/Lagos'));
        $camp[] = [
            'id' => $campaign_id,
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
            'adslots' => count($query),
        ];


        $save_campaign = Utilities::switch_db('api')->table('campaigns')->insert($camp);

        if($save_campaign){
            $camp_id = Utilities::switch_db('api')->select("SELECT id from campaigns WHERE id='$campaign_id'");
            foreach($query as $q)
            {
//            $adslot = Utilities::switch_db('api')->select("SELECT id from adslots where id='$q->rate_id'");
                $new_q[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'file_name' => $q->file,
                    'broadcaster_id' => $broadcaster,
                    'file_url' => $q->file,
                    'adslot' => $q->adslot_id,
                    'user_id' => $walkins,
                    'file_code' => mt_rand(100000, 10000000).uniqid(),
                    'time_created' => date('Y-m-d H:i:s', $now),
                    'time_modified' => date('Y-m-d H:i:s', $now),
                    'time_picked' => $q->time,
                ];
            }

            $pay[] = [
                'id' => $pay_id,
                'campaign_id' => $camp_id[0]->id,
                'payment_method' => $request->payment,
                'amount' => (integer) $request->total,
                'time_created' => $now,
                'time_modified' => $now,
                'broadcaster' => $broadcaster,
                'walkins_id' => $walkin_id[0]->id,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
            ];

            $save_payment = Utilities::switch_db('api')->table('payments')->insert($pay);

            $save_file = Utilities::switch_db('api')->table('files')->insert($new_q);

            if($save_payment && $save_file){
                $payment_id = Utilities::switch_db('api')->select("SELECT id from payments WHERE id='$pay_id'");

                $invoice[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'user_id' => $walkins,
                    'payment_id' => $payment_id[0]->id,
                    'broadcaster_id' => $broadcaster,
                    'invoice_number' => rand(10000, 10000000),
                    'actual_amount_paid' => (integer) $request->total,
                    'refunded_amount' => 0,
                    'walkins_id' => $walkin_id[0]->id,
                ];

                $mpo[] = [
                    'id' => uniqid(),
                    'campaign_id' => $camp_id[0]->id,
                    'broadcaster_id' => $broadcaster,
                    'discount' => 0,
                ];

                $save_invoice = Utilities::switch_db('api')->table('invoices')->insert($invoice);

                $save_mpo = Utilities::switch_db('api')->table('mpos')->insert($mpo);

                if($save_invoice && $save_mpo){
                    foreach ($query as $q){
                        $get_slots = Utilities::switch_db('api')->select("SELECT * from adslots WHERE id = '$q->adslot_id'");
                        $id = $get_slots[0]->id;
                        $time_difference = $get_slots[0]->time_difference;
                        $time_used = $get_slots[0]->time_used;
                        $time = $q->time;
                        $new_time_used = $time_used + $time;
                        if($time_difference === $new_time_used){
                            $slot_status = 1;
                        }else{
                            $slot_status = 0;
                        }
                        $update_slot = Utilities::switch_db('api')->update("UPDATE adslots SET time_used = '$new_time_used', is_available = '$slot_status' WHERE id = '$id'");
                    }

                    $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$walkins'");
                    $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$walkins'");
                    Session::forget('step2');
                    return 'success';
                }
            }

        }else{

            return 'error';
        }

    }


}
