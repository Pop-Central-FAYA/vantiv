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

    public function checkUserTypeOnLogin()
    {  
        if(Auth::guard('web')->user()->company_type == CompanyTypeName::AGENCY){
            session()->forget('broadcaster_id');
            session(['agency_id' => Auth::guard('web')->user()->companies->first()->id]);
        }else{
           return redirect()->to(route('login'))
            ->with('error', ClassMessages::INVALID_EMAIL_PASSWORD);
        }
        
    }


}
