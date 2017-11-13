<?php

namespace Vanguard\Libraries;

use Ixudra\Curl\Facades\Curl;
use Vanguard\ApiLog;
use Vanguard\Http\Requests\Request;

Class Api
{
    protected $key;

    private $api_private_key = "nzrm64jtj9srsjab";
    private $url = "https://faya-staging.herokuapp.com/api/v1/";
    private $public = "FayaGB3DOTDBEFCUE7KEOVCS42LEMFIXQ6Z6FY2USRL3G4UTM5K3";

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public static function auth_user(Request $request)
    {

        $username = Encrypt::encrypt($request->email, "nzrm64jtj9srsjab", 256);
        $password = Encrypt::encrypt($request->password, "nzrm64jtj9srsjab", 256);
//https://faya-staging.herokuapp.com/api/v1/user/login?key=FayaGB3DOTDBEFCUE7KEOVCS42LEMFIXQ6Z6FY2USRL3G4UTM5K3
        $auth_url = "https://faya-staging.herokuapp.com/api/v1/user/login?key=FayaGB3DOTDBEFCUE7KEOVCS42LEMFIXQ6Z6FY2USRL3G4UTM5K3";

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
        return $response;
    }
}