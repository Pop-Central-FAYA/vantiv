<?php

namespace Vanguard\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Ixudra\Curl\Facades\Curl;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $api = Curl::to("http://ec2-34-239-105-98.compute-1.amazonaws.com:8000")->get();
        $api1 = $api ? $api : \GuzzleHttp\json_encode(['status' => 'notOk']);
        $api2 = json_decode($api1);
        if($api2->status === true) {
            return $next($request);
        }else{
            return redirect()->route('errors');
        }

    }


}
