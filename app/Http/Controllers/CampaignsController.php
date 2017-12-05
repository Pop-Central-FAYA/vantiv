<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Libraries\Api;
use Vanguard\Cloudinary;



class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $campaign_all = Api::getCampaignByBroadcaster();
        if($campaign_all->status === true)
        {
            $campaign = $campaign_all->data;
            return view('campaign.index')->with('campaign', $campaign);
        }

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

    public function createStep2($id)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $day_parts = $obj_preloaded->data->day_parts;
        $campaign_type = $obj_preloaded->data->campaign_types;
        return view('campaign.create2')->with('day_parts', $day_parts)->with('step2', Session::get('step2'));
    }

    public function createStep3($id)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $target_audience = $obj_preloaded->data->target_audience;
        return view('campaign.create3')->with('target_audience', $target_audience)->with('step3', Session::get('step3'));
    }

    public function createStep4($id)
    {
        $time = Api::get_time();
        $obj_time = json_decode($time);
        $time_sec = $obj_time->data;
        return view('campaign.create4')->with('time_in_sec', $time_sec);
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
//        $getCampaign = Api::getCampaignByBroadcaster();
//        $campaign = $getCampaign->data;
//
//        $all_adslot = Api::get_adslot();
//        $adslot = json_decode($all_adslot);
//        $count = count(($adslot->data));
        return view('campaign.create6');
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

    public function postStep2(Request $request, $id)
    {
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $target_audience = $obj_preloaded->data->target_audience;

        $this->validate($request, [
            'name' => 'required',
            'brand' => 'required',
            'product' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'dayparts' => 'required_without_all',
        ]);
        $step2_req = ((object) $request->all());
        session(['step2' => $step2_req]);

        return redirect()->route('campaign.create3', ['id' => $id])->with('target_audience', $target_audience);

    }

    public function postStep3(Request $request, $id)
    {
        $time = Api::get_time();
        $obj_time = json_decode($time);
        $time_sec = $obj_time->data;

        $this->validate($request, [
            'target_audience' => 'required',
            'min_age' => 'required',
            'max_age' => 'required',
            'region' => 'required_without_all'
        ]);

        $step3_req = ((object) $request->all());

        session(['step3' => $step3_req]);

        return redirect()->route('campaign.create4', ['id' => $id])->with('time', $time_sec);
    }

    public function postStep4(Request $request, $id)
    {
//        dd($request->all());
//        $getCampaign = Api::getCampaignByBroadcaster();
//        $campaign = $getCampaign->data;

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

    /**
     * @param Request $request
     * @return string
     */
    public function postCart(Request $request)
    {
//        return $request->rate_id;
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
        $user_id = Session::get('user_id');

        $calc = \DB::select("SELECT SUM(price) as total_price FROM carts WHERE user_id = '$user_id'");
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$user_id'");

        return view('campaign.create9')->with('first_session', $first)
            ->with('second_session', $second)
            ->with('calc', $calc)
            ->with('day_part', $day_parts)
            ->with('target', $targets)->with('query', $query);
    }

    Public function postCampaign(Request $request)
    {
        $campaign = json_decode(Api::storeCampaign($request));
        if($campaign->status === false)
        {
            return redirect()->back()->with('error', $campaign->message);
        }else{
            $user_id = Session::get('user_id');
            $del_cart = \DB::select("DELETE FROM carts WHERE user_id = '$user_id'");
            $del_uplaods = \DB::select("DELETE FROM uploads WHERE user_id = '$user_id'");
//            $first = Session::get('step2');
//            $second = Session::get('step3');
//            $third = Session::get('step4');
//            $request->session()->forget("step2");
//            $request->session()->forget("step3");
//            $request->session()->forget("step4");
            return redirect()->route('dashboard')->with('success', trans('app.campaign_created'));
        }
    }

    public function removeCart($id)
    {
        $rate_id = $id;
        $del = \DB::select("DELETE FROM carts WHERE rate_id = '$rate_id'");
        return redirect()->back()->with('success', trans('app.campaign'));
    }
}
