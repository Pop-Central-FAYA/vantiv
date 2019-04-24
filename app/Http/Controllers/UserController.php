<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Services\RolesPermission\ListRoleGroup;

class UserController extends Controller
{
    use CompanyIdTrait;
    public function inviteUser()
    {
        $role_list_services = new ListRoleGroup('ssp');
        $roles = $role_list_services->getRoles();
        if(!\Auth::user()->hasRole('ssp.super_admin')){
            $roles = collect($roles)->filter(function($role) {
                return $role['role'] !== 'ssp.super_admin';
            });
        }
        return view('users.invite_user')
                    ->with('roles', $roles)
                    ->with('companies', $this->getCompaniesDetails($this->companyId()));
    }
}
