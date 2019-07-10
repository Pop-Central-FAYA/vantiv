<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Auth\AuthController as MainAuthController;

use Vanguard\Http\Requests\Auth\LoginRequest;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Enum\CompanyTypeName;
use Auth;
use Illuminate\Http\Request;


class AuthController extends MainAuthController
{
    public function getLayouts()
    {
        $this->login_layout = "auth.dsp.login";
        $this->forget_password='auth.dsp.password.forget_password';
        $this->change_password ='auth.dsp.password.change_password';
        $this->dashboard_route= 'dashboard';
    }
    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    { 
        // In case that request throttling is enabled, we have to check if user can perform this request.
        // We'll key this by the username and the IP address of the client making these requests into this application.
        $throttles = settings('throttle_enabled');
        $credentials = $this->getCredentials($request);
        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        $validate = $this->loginValidation($request, $throttles,$credentials,$user);

       Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember);

        if(Auth::guard('web')->user()->status === 'Unconfirmed' || Auth::guard('web')->user()->status === ''){
           Auth::logout();
        }

       if(Auth::guard('web')->user()->company_type == CompanyTypeName::AGENCY){
            session()->forget('broadcaster_id');
            session(['agency_id' => Auth::guard('web')->user()->companies->first()->id]);
        }else{
           return redirect()->to(route('login')  . $to)
            ->with('error', ClassMessages::INVALID_EMAIL_PASSWORD);
        }

        return $this->handleUserWasAuthenticated($request, $throttles, $user);

    }


}
