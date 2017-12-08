<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Facades\Session;
use Ixudra\Curl\Facades\Curl;
use Vanguard\ApiLog;
use Vanguard\Http\Requests\Request;

Class Api
{
    public static $key;

    public static $api_private_key = "nzrm64jtj9srsjab";
    public static $url = "http://ec2-34-239-105-98.compute-1.amazonaws.com:8000/api/v1/";
    public static $public = "FayaGB3DOTDBEFCUE7KEOVCS42LEMFIXQ6Z6FY2USRL3G4UTM5K3";

    public function __construct()
    {
        Api::$api_private_key = $GLOBALS['api_private_key'];
        Api::$url = $GLOBALS['url'];
        Api::$public = $GLOBALS['public'];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public static function auth_user(Request $request)
    {
        $username = Encrypt::encrypt($request->email, Api::$api_private_key, 256);
        $password = Encrypt::encrypt($request->password, Api::$api_private_key, 256);
        $auth_url = Api::$url.'user/login?key='.Api::$public;
        $response = Curl::to($auth_url)
            ->withData([
                'email' => $username,
                'password' => $password
            ])
            ->post();
        $req = [

            'email' => $username,
            'password' => $password
        ];
        ApiLog::save_activity_log($req, $response, $auth_url);
        $data = json_decode($response, true);
        session(['encrypted_token' => $data['data']['token']]);
        session(['broadcaster_id' => $data['data']['info']['id']]);
        session(['broadcaster_brand' => $data['data']['info']['brand']]);
        $tok = Encrypt::decrypt(Session::get('encrypted_token'), Api::$api_private_key, 256);
        session(['token' => $tok]);
        $token = Session::get('token');
        session(['expired_at' => $token, 'expiry_date']);
        session(['url' => Api::$url]);
        session(['url' => Api::$url]);
        session(['user_id' => self::explode_token($token, 'id')]);

    }
    public static function get_hourly_range()
    {
        $url = Api::$url.'ratecard/hourly/range?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();

        $req = json_encode([
            'key' => Api::$public,
            'token' => $token
        ]);
        $data = json_encode($response, true);
        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }
    public static function get_time()
    {
        $url = Api::$url.'ratecard/time?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();
        $req = json_encode([
            'key' => Api::$public,
            'token' => $token
        ]);
        $data = json_encode($response, true);
        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }
    public static function get_discount_type()
    {
        $url = Api::$url.'ratecard/discount?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();
        $req = json_encode([
            'key' => Api::$public,
            'token' => $token
        ]);
        $data = json_encode($response, true);
        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }
    public static function get_ratecard_preloaded()
    {
        $url = Api::$url.'ratecard/preload/data?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();
        return json_decode($response);

    }
    public static function store_ad_slot($request)
    {
        $day = $request->days;
        $premium = (boolean)json_decode(strtolower($request->premium));
        $start_date = strtotime($request->start_date);
        $end_date = strtotime($request->end_date);
        $hourly_range = $request->hourly_range;
        $day = $request->days;
        $time = $request->time;
        $price = $request->price;
        $time_id = $request->time_id;
        $url = Api::$url.'ratecard/create?key='.Api::$public;
        $enc_token = Session::get('encrypted_token');
        $token = Session::get('token');
        $time_data = [];
        $i = 0;
        $price_array = [];
        $a = 0; $aa = 0; $bb = 0; $cc = 0; $b = 0; $c = 0; $d = 0; $e = 0; $f = 0; $g = 0;
        $h = 0; $i = 0; $j = 0; $k = 0; $l = 0; $m = 0; $n = 0; $o = 0; $p = 0; $q = 0;
        $r = 0; $s = 0; $t = 0; $u = 0; $v = 0; $w = 0; $x = 0; $y = 0; $z = 0;
        foreach($request->price_60 as $p60){
            $price = $p60;
            $price_array[] = (object) [
                'from_time' => $request->from_time[$i++],
                'price' => (integer) $price,
                'to_time' => $request->to_time[$j++],
                'time_in_seconds' => 60,
                'day_part_id' => $request->dayparts[$k++],
                'target_audience_id' => $request->target_audience[$l++],
                'region_id' => $request->region[$m++],
                'min_age' => (integer) $request->min_age[$o++],
                'max_age' => (integer) $request->max_age[$p++],
                'is_premium' => 0,
                'premium_percent' => 0,
            ];
        }
        foreach($request->price_45 as $p45){
            $price_array[] = (object) [
                'from_time' => $request->from_time[$q++],
                'price' => (integer) $p45,
                'to_time' => $request->to_time[$r++],
                'time_in_seconds' => 45,
                'day_part_id' => $request->dayparts[$s++],
                'target_audience_id' => $request->target_audience[$t++],
                'region_id' => $request->region[$u++],
                'min_age' => (integer) $request->min_age[$v++],
                'max_age' => (integer) $request->max_age[$w++],
                'is_premium' => 0,
                'premium_percent' => 0,
            ];
        }
        foreach($request->price_30 as $p30){
            $price_array[] = (object) [
                'from_time' => $request->from_time[$x++],
                'price' => (integer) $p30,
                'to_time' => $request->to_time[$y++],
                'time_in_seconds' => 30,
                'day_part_id' => $request->dayparts[$z++],
                'target_audience_id' => $request->target_audience[$a++],
                'region_id' => $request->region[$b++],
                'min_age' => (integer) $request->min_age[$c++],
                'max_age' => (integer) $request->max_age[$d++],
                'is_premium' => 0,
                'premium_percent' => 0,
            ];
        }
        foreach($request->price_15 as $p15){
            $price_array[] = (object) [
                'from_time' => $request->from_time[$e++],
                'price' => (integer) $p15,
                'to_time' => $request->to_time[$f++],
                'time_in_seconds' => 15,
                'day_part_id' => $request->dayparts[$g++],
                'target_audience_id' => $request->target_audience[$h++],
                'region_id' => $request->region[$aa++],
                'min_age' => (integer) $request->min_age[$bb++],
                'max_age' => (integer) $request->max_age[$cc++],
                'is_premium' => 0,
                'premium_percent' => 0,
            ];
        }
        $data = [
            'user_id' => self::explode_token($token, 'id'),
            'broadcaster_id' => Session::get('broadcaster_id'),
            'hourly_range_id' => $hourly_range,
            'day' => $day,
            'adslots' => $price_array,
        ];
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->withData([
                'user_id' => self::explode_token($token, 'id'),
                'broadcaster_id' => Session::get('broadcaster_id'),
                'hourly_range_id' => $hourly_range,
                'day' => $day,
                'adslots' =>  $price_array,
            ])->asJson()
            ->post();
        $req = [
            'day' => $request->days,
            'user_id' => self::explode_token($token, 'id'),
            'broadcaster_id' => Session::get('broadcaster_id'),
            'hourly_range_id' => $request->hourly_range,
            'rate' => json_encode($price_array),
        ];
        ApiLog::save_activity_log($req, json_encode($response), $url);
        session(['adslot_data' => $response]);
        return $response;

    }
    public static function get_adslot()
    {
        $url = Api::$url.'ratecard/all/'.Session::get('broadcaster_id').'?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();
        $req = json_encode([
            'key' => Api::$public,
            'token' => $token
        ]);
        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }
    public static function get_adslot_by_region($region_id)
    {
        $url = Api::$url.'ratecard/by/'.$region_id.'/'.Session::get('broadcaster_id').'?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();
        $req = json_encode([
            'key' => Api::$public,
            'token' => $token
        ]);
        ApiLog::save_activity_log($req, $response, $url);
        return $response;

    }
    public static function update_adslot($request)
    {
        $ratecard_id = $request->ratecard_id;
        $url = Api::$url.'ratecard/'.$ratecard_id.'?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $user_id = self::explode_token($token, 'id');
        $day = $request->day;
        $hourly_range = $request->hourly_range_id;
        $price_array = [];
        $j = 0;
        $i = 0;
        if(!empty((integer) $request->premium_percent))
        {
            $is_pre = 1;
        }else{
            $is_pre = 0;
        }
        foreach ($request->time as $t) {

            $price_array[] = (object) [
                'time_in_seconds' => (integer) $t,
                'price' => (integer) $request->price[$j++],
                'region_id' => $request->region_id,
                'min_age' => (integer) $request->min_age,
                'max_age' => (integer) $request->max_age,
                'adslot_id' => $request->adslot_id[$i++],
                'premium_percent' => (integer) $request->premium_percent,
                'is_premium' => $is_pre,
                'day_part_id' => $request->day_part_id,
                'target_audience_id' => $request->target_audience_id,
                'from_time' => (explode(" - ", $request->from_to_time))[0],
                'to_time' => (explode(" - ", $request->from_to_time))[1],
            ];

        }
        $json_data = [
            'user_id' => self::explode_token($token, 'id'),
            'broadcaster_id' => Session::get('broadcaster_id'),
            'hourly_range_id' => $hourly_range,
            'day' => $day,
            'adslots' => $price_array,
        ];
//        dd($json_data);
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->withData([
                'user_id' => self::explode_token($token, 'id'),
                'broadcaster_id' => Session::get('broadcaster_id'),
                'hourly_range_id' => $hourly_range,
                'day' => $day,
                'adslots' => $price_array,
            ])->asJson()
            ->put();
//        dd($response);
        ApiLog::save_activity_log($json_data, json_encode($response), $url);
        return $response;
    }
    public static function add_walkins($request)
    {
        $email = $request->email;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $phone_number = $request->phone_number;
        $url = Api::$url.'walkins/create?key='.Api::$public;
        $enc_token = Session::get('encrypted_token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->withData([
                'broadcaster_id' => Session::get('broadcaster_id'),
                'email' => $email,
                'firstname' => $first_name,
                'lastname' => $last_name,
                'phone' => $phone_number,
            ])->post();

        $req = [
            'email' => $email,
            'firstname' => $first_name,
            'lastname' => $last_name,
            'email' => $email,
            'token' => $enc_token,
            'broadcaster_id' => Session::get('broadcaster_id'),
        ];

        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }
    public static function get_walkins()
    {
        $url = Api::$url.'walkIns/'.Session::get('broadcaster_id').'?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();
        $req = json_encode([
            'key' => Api::$public,
            'token' => $token
        ]);
        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }
    public static function delete_walkins($id)
    {
        $url = Api::$url.'walkIns/'.Session::get('broadcaster_id').'/'.$id.'?key='.Api::$public;
        $enc_token = Session::get('encrypted_token');
        $token = Session::get('token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->delete();
        $req = json_encode([
            'key' => Api::$public,
            'token' => $token
        ]);
        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }
    public static function getPreloaded(){
        $url = Api::$url.'campaign/preload?key='.Api::$public;
        $enc_token = Session::get('encrypted_token');
        $token = Session::get('token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();

        $req = ([
            'url' => $url,
            'key' => Api::$public,
            'token' => $token
        ]);

        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }
    public static function getCampaignByBroadcaster()
    {
        $url = Api::$url.'campaign/broadcaster/'.Session::get('broadcaster_id').'?key='.Api::$public;
        $enc_token = Session::get('encrypted_token');
        $token = Session::get('token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();
        $req = ([
            'url' => $url,
            'key' => Api::$public,
            'token' => $token
        ]);

        ApiLog::save_activity_log($req, $response, $url);
        return (json_decode($response));
    }

    public static function storeCampaign($request)
    {
        $first = Session::get('step2');
        $second = Session::get('step3');
        $user_id = Session::get('user_id');
        $query = \DB::select("SELECT * FROM carts WHERE user_id = '$user_id'");
        $data = \DB::select("SELECT * from uploads WHERE user_id = '$user_id'");
        $preloaded = Api::getPreloaded();
        $obj_preloaded = json_decode($preloaded);
        $dayparts = implode(',', $first->dayparts);
        $region = implode(',', $second->region);
        $campaign_type_id = $obj_preloaded->data->campaign_types[1]->id;
        $request->all();
        $url = Api::$url.'campaign/create/walkins?key='.Api::$public;
        $enc_token = Session::get('encrypted_token');
        $token = Session::get('token');
        $new_q = [];
        $i = 0;
        foreach($query as $q)
        {
            $new_q[] = [
                'file_name' => $q->file,
                'file_url' => $q->file,
                'rate_id' => $q->rate_id,
            ];
        }
        $thisss = [
            'campaign_type_id' => $campaign_type_id,
            'user_id' => $user_id,
            'channel' => 'TV',
            'target_audience_id' => $second->target_audience,
            'brand' => $first->brand,
            'day_parts' => $dayparts,
            'regions' => $region,
            'start_date' => strtotime($first->start_date),
            'stop_date' => strtotime($first->end_date),
            'maximum_age' => (integer) $second->max_age,
            'minimum_age' => (integer) $second->min_age,
            'name' => $first->name,
            'product' => $first->product,
            'payment_method' => $request->payment,
            'broadcaster_id' => Session::get('broadcaster_id'),
            'amount_paid' => (integer) $request->total,
            'file_rate_object' => json_encode($new_q),
        ];
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->withData([
                'campaign_type_id' => $campaign_type_id,
                'user_id' => $user_id,
                'channel' => 'TV',
                'target_audience_id' => $second->target_audience,
                'brand' => $first->brand,
                'day_parts' => $dayparts,
                'regions' => $region,
                'start_date' => strtotime($first->start_date),
                'stop_date' => strtotime($first->end_date),
                'maximum_age' => (integer) $second->max_age,
                'minimum_age' => (integer) $second->min_age,
                'name' => $first->name,
                'product' => $first->product,
                'payment_method' => $request->payment,
                'broadcaster_id' => Session::get('broadcaster_id'),
                'amount_paid' => (integer) $request->total,
                'file_rate_object' => json_encode($new_q),
            ])
            ->post();
        $req = [
            'campaign_type_id' => $campaign_type_id,
            'user_id' => $user_id,
            'channel' => 'TV',
            'target_audience_id' => $second->target_audience,
            'brand' => $first->brand,
            'day_parts' => $dayparts,
            'regions' => $region,
            'start_date' => strtotime($first->start_date),
            'stop_date' => strtotime($first->end_date),
            'maximum_age' => (integer) $second->max_age,
            'minimum_age' => (integer) $second->min_age,
            'name' => $first->name,
            'product' => $first->product,
            'payment_method' => $request->payment,
            'broadcaster_id' => Session::get('broadcaster_id'),
            'amount_paid' => (integer) $request->total,
            'file_rate_object' => json_encode($new_q),
        ];

        ApiLog::save_activity_log($req, $response, $url);
        return $response;

    }

    public static function getTargetAudience()
    {
        $url = Api::$url.'campaign/target-audience?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->get();
        return $response;
//        dd($response);
    }

    //Exploding the content of the token
    public static function explode_token($token, $type)
    {
        $exploded_token = explode(",", $token);
        switch ($type){
            case 'id':
                $id = $exploded_token;
                return $id[1];
                break;
            case 'name':
                $name = $exploded_token;
                return $name[3];
                break;
            case 'row':
                $row = $exploded_token;
                return $row[2];
                break;
            case 'expiry_date':
                $row = $exploded_token;
                return $row[5];
                break;
            default:
                return $exploded_token;
        }

    }

    public static function session_id(){
        $id = strtotime(date('Y-m-d H:i:s')).mt_rand(1000000000000,999999999999999);
        return $id;
    }

}