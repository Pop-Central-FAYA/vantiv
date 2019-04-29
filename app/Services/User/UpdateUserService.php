<?php

namespace Vanguard\Services\User;

use Vanguard\User;

class UpdateUserService
{
    protected $roles;
    protected $companies;
    protected $user_id;

    public function __construct($roles, $companies, $user_id)
    {
        $this->roles = $roles;
        $this->companies = $companies;
        $this->user_id = $user_id;
    }

    public function updateUser()
    {
        $user = User::find($this->user_id);
        \DB::transaction(function () use ($user) {
           $user->syncRoles($this->roles);
           if($this->companies!== ''){
               $user->companies()->sync($this->companies);
           }
        });
        return $user;
    }
}
