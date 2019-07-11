<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Http\Controllers\Auth\AuthController as MainAuthController;

class AuthController extends MainAuthController
{
    public function getLayouts()
    {
        $this->login_layout = "auth.dsp.login";
        $this->forget_password = 'auth.dsp.password.forget_password';
        $this->change_password = 'auth.dsp.password.change_password';
        $this->dashboard_route = 'dashboard';
    }

    protected function isRightUser($user) 
    {
        return $user->company_type == CompanyTypeName::AGENCY;
    }

    protected function setUserSession($user)
    {
        session()->forget('broadcaster_id');
        session(['agency_id' => $user->companies->first()->id]);
    }
}
