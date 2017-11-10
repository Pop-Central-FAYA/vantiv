<?php

namespace Vanguard\Libraries;

use Ixudra\Curl\Facades\Curl;
use Vanguard\ApiLog;
use Vanguard\Http\Requests\Request;

Class Api
{
    protected $key;

    public function __construct()
    {
        $this->key = env('ENCRYPTION_KEY');
    }

    public static function auth_user(Request $request)
    {
        $username = Encrypt::encrypt($request->username, env('ENCRYPTION_KEY'), 256);
        $password = Encrypt::encrypt($request->password, env('ENCRYPTION_KEY'), 256);
//https://faya-staging.herokuapp.com/api/v1/user/login?key=FayaGB3DOTDBEFCUE7KEOVCS42LEMFIXQ6Z6FY2USRL3G4UTM5K3
        $auth_url = env('API_URL') . 'user/login?key=' . env('API_PUBLIC_KEY');
        $response = Curl::to($auth_url)
            ->withData([
                'username' => $username,
                'password' => $password
            ])
            ->post();

        $req = [
            'username' => $username,
            'password' => $password
        ];

        ApiLog::create([
            'request' => $req,
            'response' => $response,
            'route' => $auth_url,
            'ref' => strtotime(date('Y-m-d H:i:s')) . mt_rand(10000, 999999)
        ]);

        return $response;
    }
}