<?php

namespace Vanguard\Services\User;

use Vanguard\Services\BaseServiceInterface;
use Vanguard\User;

class PermittedUser implements BaseServiceInterface
{
    protected $company_id;
    protected $permissions;

    public function __construct($company_id, $permissions)
    {
        $this->company_id = $company_id;
        $this->permissions = $permissions;
    }

    public function run()
    {
        $users = User::where('status', 'Active')->permission($this->permissions)->get(); 
        return $users->filter(function ($item) {
            if($item->companies->first()->id == $this->company_id){
                return $item;
            }
        })->values();
    }
}