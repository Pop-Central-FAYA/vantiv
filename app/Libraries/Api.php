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
        $overall_price = (int) $request->overall_price;
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
        foreach ($request->time_id as $time) {
            $time_data[] = [
                'time' => $time,
                'price' => (int) $request->price[$i++],
                'premium_price' => 0
            ];
        }
        $json_data = [
            'user_id' => self::explode_token($token, 'id'),
            'broadcaster_id' => Session::get('broadcaster_id'),
            'overall_price' => $overall_price,
            'hourly_range_id' => $hourly_range,
            'is_premium' => $premium,
            'premium_start_date' => $start_date,
            'premium_end_date' => $end_date,
            'rates' => $time_data,
        ];
        $response = Curl::to($url)
                    ->withHeader("token: $enc_token")
                    ->withData([
                        'user_id' => self::explode_token($token, 'id'),
                        'broadcaster_id' => Session::get('broadcaster_id'),
                        'overall_price' => $overall_price,
                        'hourly_range_id' => $hourly_range,
                        'day' => $day,
                        'is_premium' => $premium,
                        'premium_start_date' => $start_date,
                        'premium_end_date' => $end_date,
                        'rates' =>  $time_data,
                    ])->asJson()
                    ->post();
        $req = [
            'day' => $request->days,
            'overall_price' => $request->overall_price,
            'user_id' => self::explode_token($token, 'id'),
            'broadcaster_id' => Session::get('broadcaster_id'),
            'hourly_range_id' => $request->hourly_range,
            'is_premium' => $request->premium,
            'premium_start_date' => $request->start_date,
            'premium_end_date' => $request->end_date,
            'rate' => $time_data
        ];
        ApiLog::save_activity_log($req, json_encode($response), $url);
        session(['adslot_data' => $response]);
        return $response;

    }

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
        $overall_price = (int) $request->overall_price;
        $day = $request->day;
        $hourly_range = $request->hourly_range;
        $is_premium = (int) $request->is_premium;
        $premium_start_date = (int) $request->premium_start_date;
        $premium_stop_date = (int) $request->premium_stop_date;
        $time_data = [];
        $i = 0;
        foreach ($request->time as $times) {
            $time_data[] = [
                'time' => $times,
                'price' => (int) $request->price[$i++],
                'premium_price' => 0
            ];
        }
        $json_data = [
            'user_id' => self::explode_token($token, 'id'),
            'broadcaster_id' => Session::get('broadcaster_id'),
            'overall_price' => $overall_price,
            'hourly_range_id' => $hourly_range,
            'is_premium' => $is_premium,
            'premium_start_date' => $premium_start_date,
            'premium_end_date' => $premium_stop_date,
            'day' => $day,
            'rates' => $time_data,
        ];
        $response = Curl::to($url)
            ->withHeader("token: $enc_token")
            ->withData([
                'user_id' => self::explode_token($token, 'id'),
                'broadcaster_id' => Session::get('broadcaster_id'),
                'overall_price' => $overall_price,
                'hourly_range_id' => $hourly_range,
                'day' => $day,
                'is_premium' => $is_premium,
                'premium_start_date' => $premium_start_date,
                'premium_end_date' => $premium_stop_date,
                'rates' =>  $time_data,
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

        ApiLog::save_activity_log($req, $response, $url);
        return (json_decode($response));
    }


}