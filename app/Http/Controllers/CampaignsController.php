<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Libraries\Api;
use Vanguard\Cloudinary;
use Vanguard\Libraries\Maths;
use Vanguard\Libraries\Utilities;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;


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
        $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE broadcaster = '$broadcaster' AND adslots > 0 ORDER BY time_created asc");
        foreach ($all_campaign as $cam)
        {
            $campaign[] = [
                'id' => $j,
                'name' => $cam->name,
                'brand' => $cam->brand,
                'product' => $cam->product,
                'start_date' => date('Y-m-d', $cam->start_date),
                'end_date' => date('Y-m-d', $cam->stop_date),
                'adslots' => $cam->adslots,
                'compliance' => '86%',
                'status' => 'True'
            ];
            $j++;
        }

        return $datatables->collection($campaign)
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

    public function createStep2($id, $walkins)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $day_parts = $obj_preloaded->data->day_parts;
        $target_audience = $obj_preloaded->data->target_audience;
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        $industry = Utilities::switch_db('api')->select("SELECT id, `name` from sectors");
        $chanel = Utilities::switch_db('api')->select("SELECT * from campaignChannels");
        if(count($preload_ratecard) === 0){
            return back()->with('error','No result found!');
        }else{
            return view('campaign.create2')->with('day_parts', $day_parts)
                                                ->with('step2', Session::get('step2'))
                                                ->with('preload', $load)
                                                ->with('target_audience', $target_audience)
                                                ->with('industry', $industry)
                                                ->with('chanel', $chanel)
                                                ->with('walkins_id', $walkins);
        }
    }

    public function createStep3($id)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $target_audience = $obj_preloaded->data->target_audience;

        $ratecard = Api::get_adslot();
        $a = (json_decode($ratecard)->data);
        $first_form = Session::get('step2');
        $target = $first_form->target_audience;
        $min_age = (integer) $first_form->min_age;
        $max_age = (integer) $first_form->max_age;
        $day_parts = $first_form->dayparts;
        $region = $first_form->region;
        $result = [];
        $count_a = count($a);
        if ($count_a !== 0){
            foreach ($a as $b){
                foreach ($b->adslots as $c){
                    foreach ($c as $d){
                        $c_region = $d->region;
                        $c_day = $d->day_parts;
                        $c_target = $d->target_audience;
                        $c_minage = $d->min_age;
                        $c_maxage = $d->max_age;
                        if (in_array($c_region->id, $region) && in_array($c_day->id, $day_parts) && $c_target->id == $target && $c_minage <= $min_age && $c_maxage >= $max_age){
                            $result[] = $d;
                        }
                    }
                }
            }
            return view('campaign.create3')->with('target_audience', $target_audience)
                                                ->with('step3', Session::get('step3'))
                                                ->with('ratecard', $a)
                                                ->with('result', $result);
        }else{
            return back()->with('error','No result found!');
        }

    }

    public function createStep4($id)
    {
        $seconds = [60, 45, 39, 15];
        return view('campaign.create4');
    }

    public function createStep5($id)
    {
        $getCampaign = Api::getCampaignByBroadcaster();
        $campaign = $getCampaign->data;
        $time = Api::get_time();
        $obj_time = json_decode($time);
        $time_sec = $obj_time->data;
        return view('campaign.create5')->with('campaign', $campaign)
            ->with('time', $time_sec);
    }

    public function createStep6($id)
    {
        $all_adslot = Api::get_adslot();
        $adslot = json_decode($all_adslot);
        $count = count(($adslot->data));
        $ratecard = Api::get_adslot();
        $a = (json_decode($ratecard)->data);
        $first_form = Session::get('step2');
        $target = $first_form->target_audience;
        $min_age = (integer) $first_form->min_age;
        $max_age = (integer) $first_form->max_age;
        $day_parts = $first_form->dayparts;
        $region = $first_form->region;
        $result = [];
        $count_a = count($a);
        if ($count_a !== 0) {
            foreach ($a as $b) {
                foreach ($b->adslots as $c) {
                    foreach ($c as $d) {
                        $c_region = $d->region;
                        $c_day = $d->day_parts;
                        $c_target = $d->target_audience;
                        $c_minage = $d->min_age;
                        $c_maxage = $d->max_age;

                        if (in_array($c_region->id, $region) && in_array($c_day->id, $day_parts) && $c_target->id == $target && $c_minage <= $min_age && $c_maxage >= $max_age) {
                            $result[] = $d;
                        }
                    }
                }
            }
        }
        return view('campaign.create6')->with('result', $result);
    }

    public function createStep7($id)
    {
        $first = Session::get('step2');
        $second = Session::get('step3');
        $adslot = Api::get_adslot();
        $a = json_decode($adslot);
        $b = (object)($a->data);
        $count = count(($a->data));

        $user_id = Session::get('user_id');
        $data = \DB::select("SELECT * from uploads WHERE user_id = '$user_id'");

        $cart = \DB::select("SELECT * from carts WHERE user_id = '$user_id'");
        return view('campaign.create7')->with('adslot', $b)
            ->with('counting', $count)
            ->with('data', $data)
            ->with('cart', $cart);
    }

    public function createStep8($id)
    {
        return view('campaign.create8');
    }

    public function createStep9($id)
    {
        return view('campaign.create9');
    }

    public function postStep2(Request $request, $id, $walkins)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $target_audience = $obj_preloaded->data->target_audience;

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
        $step2_req = ((object) $request->all());
        session(['step2' => $step2_req]);
        session(['walkins_id' => $walkins]);
        return redirect()->route('campaign.create3', ['id' => $id])->with('target_audience', $target_audience);
    }

    public function postStep3(Request $request, $id)
    {
        $time = Api::get_time();
        $obj_time = json_decode($time);
        $time_sec = $obj_time->data;
        $step3_req = ((object) $request->all());
        session(['step3' => $step3_req]);
        return redirect()->route('campaign.create4', ['id' => $id])->with('time', $time_sec);
    }

    public function postStep4(Request $request, $id)
    {
        $adslot = Api::get_adslot();
        $big_array = (json_decode($adslot)->data);

        $this->validate($request, [
            'uploads' => 'required',
            'time' => 'required'
        ]);
        $user_id = Session::get('user_id');
        $lenght = count($request->time);

        if ($request->file('uploads')) {
            foreach ($request->uploads as $k => $filesUploaded) {
                $DestinationPath = 'campaign_files';
                $FileName = $filesUploaded->getClientOriginalName();
                $FileExtension = $filesUploaded->getClientOriginalExtension();
                $filesUploaded->move($DestinationPath, $FileName);
                $file_name = 'campaign_files/' . $FileName;

                $time = $request->time[$k];
                \DB::select("INSERT into uploads (user_id,`time`,uploads) VALUES ('$user_id','$time','$file_name')");
            }
            $preloaded = Api::getPreloaded();
            $obj_preloaded = json_decode($preloaded);
            $first = Session::get('step2');
            $second = Session::get('step3');
            return redirect()->route('campaign.create6', ['id' => $id]);
        }
    }

    public function getStep7($id)
    {
        $first = Session::get('step2');
        $second = Session::get('step3');
        $ratecard = Api::get_adslot();
        $a = (json_decode($ratecard)->data);
        $first_form = Session::get('step2');
        $target = $first_form->target_audience;
        $min_age = (integer) $first_form->min_age;
        $max_age = (integer) $first_form->max_age;
        $day_parts = $first_form->dayparts;
        $region = $first_form->region;
        $result = [];
        $count_a = count($a);
        if ($count_a !== 0) {
            foreach ($a as $b) {
                foreach ($b->adslots as $c) {
                    foreach ($c as $d) {
                        $c_region = $d->region;
                        $c_day = $d->day_parts;
                        $c_target = $d->target_audience;
                        $c_minage = $d->min_age;
                        $c_maxage = $d->max_age;
                        if (in_array($c_region->id, $region) && in_array($c_day->id, $day_parts) && $c_target->id == $target && $c_minage <= $min_age && $c_maxage >= $max_age) {
                            $result[] = $d;
                        }
                    }
                }
            }
        }
        $user_id = Session::get('user_id');
        $data = \DB::select("SELECT * from uploads WHERE user_id = '$user_id'");
        $cart = \DB::select("SELECT * from carts WHERE user_id = '$user_id'");
        return view('campaign.create7')->with('ratecard', $a)
            ->with('data', $data)
            ->with('cart', $cart)
            ->with('result', $result);
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
            'rate_id' => 'required|unique:carts',
        ]);
        $price = $request->price;
        $file = $request->file;
        $time = $request->time;
        $id = $request->rate_id;
        $hourly_range = $request->range;
        $user = Session::get('user_id');
        $broadcaster = Session::get('broadcaster_id');
        $ip = \Request::ip();
        $insert = \DB::insert("INSERT INTO carts (user_id, broadcaster_id, price, ip_address, file, from_to_time, `time`, rate_id) VALUES ('$user','$broadcaster','$price','$ip','$file','$hourly_range','$time','$id')");
        if($insert){
            return "success";
        }else{
            return "failure";
        }
    }

    public function getCheckout()
    {
        $first = Session::get('step2');
        $second = Session::get('step3');
        $third = Session::get('step4');
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $day_parts = $obj_preloaded->data->day_parts;
        $targets = $obj_preloaded->data->target_audience;
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        $user_id = Session::get('user_id');
        $first = Session::get('step2');
        $calc = \DB::select("SELECT SUM(price) as total_price FROM carts WHERE user_id = '$user_id'");
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$user_id'");
        return view('campaign.create9')->with('first_session', $first)
            ->with('second_session', $second)
            ->with('calc', $calc)
            ->with('day_part', $day_parts)
            ->with('target', $targets)->with('query', $query)->with('first', $first)
            ->with('preload', $load);
    }

    Public function postCampaign(Request $request)
    {
        $first = Session::get('step2');
        $second = Session::get('step3');
        $user_id = Session::get('user_id');
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$user_id'");
        $data = \DB::select("SELECT * from uploads WHERE user_id = '$user_id'");
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $request->all();
        $url = Api::$url.'campaign/create/walkins?key='.Api::$public;
        $enc_token = Session::get('encrypted_token');
        $token = Session::get('token');
        $new_q = [];
        $pay = [];
        $camp = [];
        $i = 0;
        $campaign_id = uniqid();
        $walkin_id = Session::get('walkinss_id');
        $db_walkin = Utilities::switch_db('api')->select("SELECT user_id from walkins WHERE id='$walkin_id'");
        $now = strtotime(Carbon::now('Africa/Lagos'));
        $camp[] = [
            'id' => $campaign_id,
            'user_id' => $user_id,
            'channel' => $first->channel,
            'brand' => $first->brand,
            'start_date' => strtotime($first->start_date),
            'stop_date' => strtotime($first->end_date),
            'name' => $first->name,
            'product' => $first->product,
            'day_parts' => implode(',', $first->dayparts),
            'broadcaster' => Session::get('broadcaster_id'),
            'target_audience' => $first->target_audience,
            'region' => implode(',', $first->region),
            'min_age' => (integer)$first->min_age,
            'max_age' => (integer)$first->max_age,
            'industry' => $first->industry,
            'adslots' => count($query),
            'walkins_id' => $db_walkin[0]->user_id,
            'time_created' => $now,
            'time_modified' => $now,
        ];

        $save_campaign = Utilities::switch_db('api')->table('campaigns')->insert($camp);
        $camp_id = Utilities::switch_db('api')->select("SELECT id from campaigns WHERE id='$campaign_id'");
        foreach($query as $q)
        {
//            $adslot = Utilities::switch_db('api')->select("SELECT id from adslots where id='$q->rate_id'");
            $new_q[] = [
                'id' => uniqid(),
                'campaign_id' => $camp_id[0]->id,
                'file_name' => $q->file,
                'file_url' => $q->file,
                'adslot' => $q->rate_id,
                'user_id' => $user_id,
                'broadcaster_id' => Session::get('broadcaster_id'),
                'file_code' => uniqid(),
                'time_created' => $now,
                'time_modified' => $now,
            ];
        }

        $pay[] = [
          'id' => uniqid(),
            'campaign_id' => $camp_id[0]->id,
            'payment_method' => $request->payment,
            'amount' => (integer) $request->total,
            'time_created' => $now,
            'time_modified' => $now,
            'broadcaster' => Session::get('broadcaster_id'),
            'walkins_id' => $db_walkin[0]->user_id,
            'time_created' => $now,
            'time_modified' => $now,
        ];

        $save_payment = Utilities::switch_db('api')->table('payments')->insert($pay);

        $save_file = Utilities::switch_db('api')->table('files')->insert($new_q);

        if($save_campaign && $save_file && $save_payment)
        {
            $user_id = Session::get('user_id');
            $del_cart = \DB::select("DELETE FROM carts WHERE user_id = '$user_id'");
            $del_uplaods = \DB::select("DELETE FROM uploads WHERE user_id = '$user_id'");
            return redirect()->route('dashboard')->with('success', 'campaign created successfully');
            Session::forget('step2');
            Session::forget('walkins_id');
        }else{
            return redirect()->back()->with('error', 'Could not create this campaign');
        }

    }

    public function removeCart($id)
    {
        $rate_id = $id;
        $del = \DB::select("DELETE FROM carts WHERE rate_id = '$rate_id'");
        return redirect()->back()->with('success', trans('app.campaign'));
    }
}
