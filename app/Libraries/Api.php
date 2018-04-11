<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Facades\Session;
use Ixudra\Curl\Facades\Curl;
use Vanguard\ApiLog;
use Vanguard\Http\Requests\Request;

Class Api
{
    public static $key;

    public static $url = "http://127.0.0.1:8000/api/";

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
        $username = $request->email;
        $password = $request->password;
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
        $status = json_decode($response);
        if ($status->status === false) {
            return back()->withErrors("Invalid Username or Password");
        }

        ApiLog::save_activity_log($req, $response, $auth_url);
        $data = json_decode($response, true);
        $status = json_decode($response);
        if($status->status == false)
        {
            return back();
        }

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
    public static function get_hourly_ranges()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM hourlyRanges");
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
//        ApiLog::save_activity_log($req, $response, $url);
        return $response;
    }

    public static function get_dayParts()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM dayParts");
    }

    public function get_all_region()
    {
        Utilities::switch_db('api')->select("SELECT * from regions");
    }

    public static function get_discountTypes()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM discount_types");
    }

    public static function get_agencies()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM agents");
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

    public static function get_agent_user($user_id)
    {
        $data = Utilities::switch_db('reports')->select("SELECT id, firstname, lastname FROM users WHERE id = '$user_id'");

        return $data;
    }

    public static function get_discount_classes()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM discount_classes");
    }
    
    public static function store_ad_slot($request)
    {
        $day = $request->days;
        $premium = (boolean)json_decode(strtolower($request->premium));
        $start_date = $request->start_date;
        $end_date = $request->end_date;
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
//        ApiLog::save_activity_log($req, $response, $url);
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
//        ApiLog::save_activity_log($req, $response, $url);
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
//        ApiLog::save_activity_log($req, $response, $url);
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

//        ApiLog::save_activity_log($req, $response, $url);
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
            'user_id' => $user_id,
            'channel' => $first->channel,
            'brand' => $first->brand,
            'start_date' => $first->start_date,
            'stop_date' => $first->end_date,
            'name' => $first->name,
            'product' => $first->product,
            'payment_method' => $request->payment,
            'broadcaster_id' => Session::get('broadcaster_id'),
            'amount_paid' => (integer) $request->total,
            'file_rate_object' => json_encode($new_q),
        ];
        dd($thisss);
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->withData([
                'user_id' => $user_id,
                'channel' => 'TV',
                'brand' => $first->brand,
                'start_date' => $first->start_date,
                'stop_date' => $first->end_date,
                'name' => $first->name,
                'product' => $first->product,
                'payment_method' => $request->payment,
                'broadcaster_id' => Session::get('broadcaster_id'),
                'amount_paid' => (integer) $request->total,
                'file_rate_object' => json_encode($new_q),
            ])
            ->post();
        $req = [
            'user_id' => $user_id,
            'channel' => 'TV',
            'brand' => $first->brand,
            'start_date' => $first->start_date,
            'stop_date' => $first->end_date,
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
        $id = date('Y-m-d H:i:s').mt_rand(1000000000000,999999999999999);
        return $id;
    }

    /**
     * Discounts
     */

    public static function get_discounts_by_type($type)
    {
        $broadcaster_id = \Session::get('broadcaster_id');

        return Utilities::switch_db('reports')->select("SELECT * FROM discounts WHERE broadcaster = '$broadcaster_id' AND discount_type = '$type' AND status = '1'");
    }

    public static function create_discount($discount_type_value, $percent_value, $percent_start_date,
                                           $percent_stop_date, $value, $value_start_date, $value_stop_date,
                                           $discount_class_id, $discount_type_id, $discount_type_sub_value)
    {
        $key = Api::$public;
        $token = Session::get('token');
        $broadcaster_id = Session::get('broadcaster_id');
        $url = Api::$url.'discount/create?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->withData([
                'broadcaster_id' => $broadcaster_id,
                'discount_type_id' => $discount_type_id,
                'discount_type_value' => $discount_type_value,
                'discount_class_id' => $discount_class_id,
                'percent_value' => $percent_value,
                'percent_start_date' => $percent_start_date,
                'percent_stop_date' => $percent_stop_date,
                'value' => $value,
                'value_start_date' => $value_start_date,
                'value_stop_date' => $value_stop_date,
                'discount_type_sub_value' => $discount_type_sub_value
            ])
            ->post();

        $req = json_encode([
            'key' => $key,
            'token' => $token,
            'broadcaster_id' => $broadcaster_id,
            'discount_type_id' => $discount_type_id,
            'discount_type_value' => $discount_type_value,
            'discount_class_id' => $discount_class_id,
            'percent_value' => $percent_value,
            'percent_start_date' => $percent_start_date,
            'percent_stop_date' => $percent_stop_date,
            'value' => $value,
            'value_start_date' => $value_start_date,
            'value_stop_date' => $value_stop_date,
            'discount_type_sub_value' => $discount_type_sub_value
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function update_discount($discount, $discount_type_value, $percent_value, $percent_start_date,
                                           $percent_stop_date, $value, $value_start_date, $value_stop_date,
                                           $discount_class_id, $discount_type_id, $discount_type_sub_value)
    {
        $key = Api::$public;
        $broadcaster_id = Session::get('broadcaster_id');
        $url = Api::$url.'discount/' . $broadcaster_id . '/' . $discount . '?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->withData([
                'broadcaster_id' => $broadcaster_id,
                'discount_type_id' => $discount_type_id,
                'discount_type_value' => $discount_type_value,
                'discount_class_id' => $discount_class_id,
                'percent_value' => $percent_value,
                'percent_start_date' => $percent_start_date,
                'percent_stop_date' => $percent_stop_date,
                'value' => $value,
                'value_start_date' => $value_start_date,
                'value_stop_date' => $value_stop_date,
                'discount_type_sub_value' => $discount_type_sub_value

            ])
            ->put();

        $req = json_encode([
            'key' => $key,
            'broadcaster_id' => $broadcaster_id,
            'discount_type_id' => $discount_type_id,
            'discount_type_value' => $discount_type_value,
            'discount_class_id' => $discount_class_id,
            'percent_value' => $percent_value,
            'percent_start_date' => $percent_start_date,
            'percent_stop_date' => $percent_stop_date,
            'value' => $value,
            'value_start_date' => $value_start_date,
            'value_stop_date' => $value_stop_date,
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function delete_discount($discount)
    {
        $key = Api::$public;
        $token = Session::get('token');
        $url = Api::$url.'discount/delete/' . $discount . '?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->delete();

        $req = json_encode([
            'key' => $key,
            'token' => $token,
            'discount_id' => $discount
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }


    /**
     * Campaigns
     */
    public static function get_broadcaster_campaign()
    {
        $key = Api::$public;
        $broadcaster_id = Session::get('broadcaster_id');
        $url = Api::$url.'campaign/broadcaster/' . $broadcaster_id . '?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->get();

        $req = json_encode([
            'key' => $key,
            'token' => Session::get('token'),
            'broadcaster_id' => $broadcaster_id
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    /**
     * MPOs
     */
    public static function get_broadcaster_mpo()
    {
        $key = Api::$public;
        $broadcaster_id = Session::get('broadcaster_id');
        $url = Api::$url.'campaign/mpos/' . $broadcaster_id . '?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->get();

        $req = json_encode([
            'key' => $key,
            'token' => Session::get('token'),
            'broadcaster_id' => $broadcaster_id
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function get_mpo_by_type($status)
    {
        $key = Api::$public;
        $broadcaster_id = Session::get('broadcaster_id');
        $url = Api::$url.'campaign/mpos/by/' . $status . '/' . $broadcaster_id . '?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->get();

        $req = json_encode([
            'key' => $key,
            'token' => Session::get('token'),
            'broadcaster_id' => $broadcaster_id
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function update_fileStatus($is_file_accepted, $broadcaster_id, $file_code, $campaign_id)
    {
        $key = Api::$public;
        $url = Api::$url.'campaign/mpos/approve/' . $is_file_accepted . '/' . $broadcaster_id . '/' . $file_code . '/' . $campaign_id . '?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->put();

        $req = json_encode([
            'key' => $key,
            'token' => Session::get('token'),
            'broadcaster_id' => $broadcaster_id,
            'is_file_accepted' => $is_file_accepted,
            'file_code' => $file_code,
            'campaign_id' => $campaign_id
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function fetchCampaign($campaign_id)
    {
        $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaignDetails WHERE campaign_id = '$campaign_id'");

        return $campaign;

    }

    public static function brand($campaign_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from brands where id = (SELECT brand from campaignDetails where campaign_id = '$campaign_id')");
    }

    public static function fetchPayment($campaign_id)
    {
        $payment = Utilities::switch_db('reports')->select("SELECT * FROM paymentDetails WHERE payment_id = (SELECT id from payments where campaign_id = '$campaign_id')");

        return $payment;
    }

    public static function getMpoByType($type)
    {
        $mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpos WHERE status = '$type'");

        return $mpos;
    }

    public static function getChannelName($channel_id)
    {
        $channel = Utilities::switch_db('reports')->select("SELECT * FROM campaignChannels WHERE id = '$channel_id'");

        return $channel;
    }

    public static function getCampaignFiles($campaign_id)
    {
        $files = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id'");

        return $files;
    }

    public static function getOutstandingFiles($campaign_id)
    {
        $files = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id' AND is_file_accepted <> 1");

        return $files;
    }

    /**
     * Sectors
     */

    public static function create_sector($name, $sector_code, $sector_id)
    {
        $key = Api::$public;
        $token = Session::get('token');
        $url = Api::$url.'sector/create?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader(["token => $encrypted_token"])
            ->withData([
                'name' => $name,
                'sector_code' => $sector_code,
                'sector_id' => $sector_id
            ])
            ->post();

        $req = json_encode([
            'name' => $name,
            'sector_code' => $sector_code,
            'token' => $token
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function get_sectors()
    {
        $key = Api::$public;
        $token = Session::get('token');
        $url = Api::$url.'sector?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->get();

        $req = json_encode([
            'key' => $key,
            'token' => $token
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function delete_sector($sector_id)
    {
        $key = Api::$public;
        $token = Session::get('token');
        $url = Api::$url.'sector/' . $sector_id . '?key='.$key;

        $response = Curl::to($url)
            ->withData([
                'sector_id' => $sector_id,
                'key' => $key,
                'token' => $token
            ])
            ->delete();

        $req = json_encode([
            'sector_id' => $sector_id,
            'key' => $key,
            'token' => $token
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function create_subsector($name, $sector_code, $sector_id)
    {
        $key = Api::$public;
        $token = Session::get('token');
        $url = Api::$url.'sector/sub-sector/create?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader("token: $encrypted_token")
            ->withData([
                'name' => $name,
                'sector_code' => $sector_code,
            ])
            ->post();

        $req = json_encode([
            'name' => $name,
            'sector_code' => $sector_code,
            'sector_id' => $sector_id,
            'token' => $token
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function remove_sub_sector($sector_id, $sub_sector_id)
    {
        $key = Api::$public;
        $token = Session::get('token');
        $url = Api::$url.'sector/subsector/remove/' . $sector_id . '/' . $sub_sector_id . '?key='.$key;
        $encrypted_token = Session::get('encrypted_token');

        $response = Curl::to($url)
            ->withHeader(["token => $encrypted_token"])
            ->put();

        $req = json_encode([
            'sector_id' => $sector_id,
            'sub_sector_id' => $sub_sector_id,
            'token' => $token
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function delete_subsector(Request $request)
    {

        $sub_sector_id = $request->sector_id;
        $key = Api::$public;
        $token = Session::get('token');
        $url = Api::$url.'sector/sub-sector/' . $sub_sector_id . '?key='.$key;

        $response = Curl::to($url)
            ->withData([
                'sub_sector_id' => $sub_sector_id,
                'key' => $key,
                'token' => $token
            ])
            ->delete();

        $req = json_encode([
            'sub_sector_id' => $sub_sector_id,
            'key' => $key,
            'token' => $token
        ]);

        ApiLog::save_activity_log($req, $response, $url);

        return $response;
    }

    public static function countClients($agency_id)
    {
        $client = Utilities::switch_db('api')->select("SELECT * from walkIns where agency_id = '$agency_id'");
        return count($client);
    }

    public static function countCampaigns($agency_id)
    {
        $client_campaigns = Utilities::switch_db('api')->select("SELECT * from campaigns where agency = '$agency_id'");
        return count($client_campaigns);
    }

    public static function countInvoices($agency_id)
    {
        $client_invoice = Utilities::switch_db('api')->select("SELECT * from invoices where agency_id = '$agency_id'");
        return count($client_invoice);
    }

    public static function countBrands($agency_id)
    {
        $brands = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$agency_id'");
        return count($brands);
    }

    public static function countApproved($agency_id)
    {
        $count_approval = Utilities::switch_db('api')->select("SELECT * from invoices where agency_id = '$agency_id' AND status = 1");
        return count($count_approval);
    }

    public static function countUnapproved($agency_id)
    {
        $count_unapproval = Utilities::switch_db('api')->select("SELECT * from invoices where agency_id = '$agency_id' AND status = 0");
        return count($count_unapproval);
    }

    public static function allInvoiceAdvertiserorAgency($agency_id)
    {
        $all_invoices = Utilities::switch_db('reports')->select("SELECT * FROM invoices WHERE  agency_id = '$agency_id' ORDER BY time_created DESC LIMIT 5");

        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

            $campaign_id = $invoice->campaign_id;

            $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaigns WHERE id = '$campaign_id'");
            $brand_id = $campaign[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");

            $invoice_campaign_details[] = [
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => $invoice->actual_amount_paid,
                'refunded_amount' => $invoice->refunded_amount,
                'status' => $invoice->status,
                'campaign_brand' => $brand_name[0]->name,
                'campaign_name' => $campaign[0]->name
            ];
        }
        return $invoice_campaign_details;
    }

    public static function saveActivity($user_id, $description, $ip, $user_agent)
    {
        $store_activity = [
            'description' => $description,
            'user_id' => $user_id,
            'ip_address' => $ip,
            'user_agent' => $user_agent
        ];

        return \DB::table('user_activity')->insert($store_activity);
    }

    public static function countFiles($advertiser_id)
    {
        $files = Utilities::switch_db('api')->select("SELECT * FROM files where agency_id = '$advertiser_id'");
        return count($files);
    }

    public static function validateCampaign()
    {
        $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE campaign_status = 0");
        $array = [];
        $adslot_arrays = [];
        foreach ($campaigns as $campaign){
            $today = strtotime(date("Y-m-d"));
            if($today > strtotime($campaign->stop_date)){
                $update_campaign = Utilities::switch_db('api')->update("UPDATE campaignDetails set campaign_status = 1 where campaign_id = '$campaign->id'");
                $adslots = Utilities::switch_db('api')->select("SELECT * from adslots where id IN ($campaign->adslots_id) ORDER BY time_created DESC");
                foreach ($adslots as $adslot){
                    $files = Utilities::switch_db('api')->select("SELECT time_picked, adslot from files where adslot = '$adslot->id' AND campaign_id = '$campaign->id'");
                    $adslot_arrays[] = [
                        'adslot_id' => $adslot->id,
                        'file' => $files,
                    ];
                }

            }
        }

        foreach ($adslot_arrays as $adslot_array){
            $adslot_id = $adslot_array['file'][0]->adslot;
            $time_picked = $adslot_array['file'][0]->time_picked;
            $slot_id = $adslot_array['adslot_id'];
            $get_adslot = Utilities::switch_db('api')->select("SELECT * from adslots where id = '$adslot_id'");
            $new_time_used = ($get_adslot[0]->time_used) - ($time_picked);
            $update_adslot = Utilities::switch_db('api')->update("UPDATE adslots set time_used = '$new_time_used' where id = '$adslot_id'");
        }

        return true;

    }

    public static function addFilesToApi($file_code)
    {
        $t = new \Vanguard\Http\Controllers\Api\DummyController();
        $s = $t->addFile($file_code);
        return $s->getData();
    }

    public static function getApiWelcome()
    {
        $t = new \Vanguard\Http\Controllers\Api\DummyController();
        $s = $t->index();
        return $s->getData();
    }

    public static function approvedCampaignFiles($campaign_id)
    {
        $allFiles = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id'");

        $approvedFiles = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id' AND is_file_accepted = 1");

        return count($allFiles) === count($approvedFiles);
    }

    public static function pendingMPOs($campaign_id)
    {
        $allFiles = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id'");
        $approvedFiles = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id' AND is_file_accepted = 1");

        return count($approvedFiles) < count($allFiles);
    }


}