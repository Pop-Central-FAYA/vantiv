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

}
