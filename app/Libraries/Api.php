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
    public static $url = "https://faya-staging.herokuapp.com/api/v1/";
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
//https://faya-staging.herokuapp.com/api/v1/user/login?key=FayaGB3DOTDBEFCUE7KEOVCS42LEMFIXQ6Z6FY2USRL3G4UTM5K3
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
//        dd($response);

        session(['encrypted_token' => $data['data']['token']]);
        session(['broadcaster_id' => $data['data']['info']['id']]);

        $tok = Encrypt::decrypt(Session::get('encrypted_token'), Api::$api_private_key, 256);
        session(['token' => $tok]);
//        $token = Session::get('token');

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

        //return $response;
        dd($response);

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
}