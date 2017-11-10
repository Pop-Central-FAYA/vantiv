<?php

namespace Vanguard\Libraries;

use Ixudra\Curl\Facades\Curl;
use Vanguard\Http\Requests\Request;

Class Api
{
    protected $key;

    public function __construct()
    {
        $this->key = env('ENCRYPTION_KEY');
    }

    public function auth_user(Request $request)
    {
        $username = Encrypt::encrypt($request->username, $this->key, 256);
        $password = Encrypt::encrypt($request->password, $this->key, 256);

        $response = Curl::to(env('API_URL'))
            ->withData([
                'username' => $username,
                'password' => $password
            ])
            ->post();

        return $response;
    }
}