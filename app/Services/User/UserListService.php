<?php

namespace Vanguard\Services\User;

use Vanguard\Models\Company;
use Vanguard\User;
use Vanguard\Services\BaseServiceInterface;
class UserListService implements BaseServiceInterface
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function run()
    {
      return $this->getUserList();
    }
    private function getUserList()
    {
         $users= User::
                    join('company_user', 'company_user.user_id', '=', 'users.id')
                    ->whereIn('company_user.company_id', $this->company_id)
                    ->select('users.*')
                    ->selectRaw("JSON_ARRAYAGG(company_user.company_id) AS company_id")
                    ->groupBy('users.id')
                    ->get();
                    return $users;
                    
    }
}
