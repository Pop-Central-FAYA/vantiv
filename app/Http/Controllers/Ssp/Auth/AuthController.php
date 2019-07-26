<?php

namespace Vanguard\Http\Controllers\Ssp\Auth;

use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Http\Controllers\Auth\AuthController as MainAuthController;

class AuthController extends MainAuthController
{
    public function getLayouts()
    {
        $this->login_layout = 'auth.ssp.login';
        $this->forget_password = 'auth.ssp.password.forget_password';
        $this->change_password = 'auth.ssp.password.change_password';
        $this->dashboard_route = 'broadcaster.dashboard.index';
        $this->email_subject = 'Torch Password Reset';
    }

    protected function isRightUser($user) 
    {
        return $user->company_type == CompanyTypeName::BROADCASTER;
    }

    protected function setUserSession($user)
    {
        session()->forget('agency_id');
        session(['broadcaster_id' => $user->companies->first()->id]);
    }
}
