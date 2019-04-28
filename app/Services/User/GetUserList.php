<?php

namespace Vanguard\Services\User;

use Vanguard\Models\Company;
use Vanguard\User;

class GetUserList
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    private function getUserList()
    {
        return User::
                    join('company_user', 'company_user.user_id', '=', 'users.id')
                    ->whereIn('company_user.company_id', $this->company_id)
                    ->select('users.*')
                    ->selectRaw("JSON_ARRAYAGG(company_user.company_id) AS company_id")
                    ->groupBy('users.id')
                    ->get();
    }

    public function getUserData()
    {
        $user_data = [];
        foreach ($this->getUserList() as $user){
            $user_data[] = [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'roles' => $this->roleLabel($user->getRoleNames()),
                'role_name' => $user->getRoleNames(),
                'company' => $this->getCompanyName($user->company_id),
                'company_id' => $user->company_id,
                'status' => $user->status
            ];
        }
        return $user_data;
    }

    private function getCompanyName($company_id_list)
    {
        if ($company_id_list) {
            $companies = Company::select('name')->whereIn('id', $company_id_list)->get();
            return $companies->implode("name", ", ");
        }
        return "";
    }

    private function roleLabel($roles)
    {
        $role_label = [];
        foreach ($roles as $role){
            $role_label[] = ucwords(str_replace('_', ' ', explode('.', $role)[1]));
        }
        return $role_label;
    }
}
