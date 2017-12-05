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
        $url = Api::$url.'adslot/hourly/range?key='.Api::$public;
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
        $url = Api::$url.'adslot/time?key='.Api::$public;
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
        $url = Api::$url.'adslot/discount?key='.Api::$public;
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
        $url = Api::$url.'adslot/create?key='.Api::$public;
        $enc_token = Session::get('encrypted_token');
        $token = Session::get('token');
        $time_data = [];
        $i = 0;
        $price_array = [];
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;
        $m = 0;
        $n = 0;
        $o = 0;
        $q = 0;
        foreach($request->price_60 as $p60){
            $price = $p60;
            $price_array[] = [
                'from_time' => $request->from[$i++],
                'price' => (integer) $price,
                'to_time' => $request->to[$j++],
                'time' => "60",
                'is_premium' => 0,
                'premium_price' => (integer) $price
            ];
        }
        foreach($request->price_45 as $p45){
            $price_array[] = [
                'from_time' => $request->from[$k++],
                'price' => (integer) $p45,
                'to_time' => $request->to[$l++],
                'time' => "45",
                'is_premium' => 0,
                'premium_price' => (integer) $p45
            ];
        }
        foreach($request->price_30 as $p30){
            $price_array[] = [
                'from_time' => $request->from[$m++],
                'price' => (integer) $p30,
                'to_time' => $request->to[$n++],
                'time' => "30",
                'is_premium' => 0,
                'premium_price' => (integer) $p30
            ];
        }
        foreach($request->price_15 as $p15){
            $price_array[] = [
                'from_time' => $request->from[$o++],
                'price' => (integer) $p15,
                'to_time' => $request->to[$q++],
                'time' => "15",
                'is_premium' => 0,
                'premium_price' => (integer) $p15
            ];
        }
        $data = [
            'user_id' => self::explode_token($token, 'id'),
            'broadcaster_id' => Session::get('broadcaster_id'),
            'hourly_range_id' => $hourly_range,
            'day' => $day,
            'overall_price' => $request->overall_price,
            'rates' => json_encode($price_array),
        ];

        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->withData([
                'user_id' => self::explode_token($token, 'id'),
                'broadcaster_id' => Session::get('broadcaster_id'),
                'hourly_range_id' => $hourly_range,
                'day' => $day,
                'rates' =>  $price_array,
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
        $url = Api::$url.'adslot/all/'.Session::get('broadcaster_id').'?key='.Api::$public;
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
        $adslot_id = $request->adslot_id;
        $url = Api::$url.'adslot/'.$adslot_id.'?key='.Api::$public;
        $token = Session::get('token');
        $enc_token = Session::get('encrypted_token');
        $user_id = self::explode_token($token, 'id');
        $day = $request->day;
        $hourly_range = $request->hourly_range_id;
        $price_array = [];
        $j = 0;
        foreach ($request->time as $t) {

            $price_array[] = [
                'time' => (integer) $t,
                'price' => (integer) $request->price[$j++],
                'from_time' => (explode(" - ", $request->from_to_time))[0],
                'to_time' => (explode(" - ", $request->from_to_time))[1],
            ];

        }
        $json_data = [
            'user_id' => self::explode_token($token, 'id'),
            'broadcaster_id' => Session::get('broadcaster_id'),
            'hourly_range_id' => $hourly_range,
            'day' => $day,
            'rates' => $price_array,
        ];
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->withData([
                'user_id' => self::explode_token($token, 'id'),
                'broadcaster_id' => Session::get('broadcaster_id'),
                'hourly_range_id' => $hourly_range,
                'day' => $day,
                'rates' => $price_array,
            ])->asJson()
            ->put();

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
//        dd(Session::get('broadcaster_id'));

//        ApiLog::save_activity_log($req, $response, $url);
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