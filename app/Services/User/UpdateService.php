<?php

namespace Vanguard\Services\User;

use Vanguard\User;

use Vanguard\Services\BaseServiceInterface;

class UpdateService implements BaseServiceInterface
{
    protected $validated;
    protected $companies;
    protected $user_id;
    protected $guard;

    public function __construct($validated, $companies, $user_id, $guard)
    {
        $this->validated = $validated;
        $this->companies = $companies;
        $this->user_id = $user_id;
        $this->guard = $guard;
    }

    public function run()
    {
        return $this->updateUser();
    }

    public function updateUser()
    {
        $roles = $this->getRoleName($this->validated['role_name']);
        $user = User::find($this->user_id);
        \DB::transaction(function () use ($user, $roles) {
           $user->syncRoles($roles, $this->guard);
           if($this->companies!== ''){
               $user->companies()->sync($this->companies);
           }
        });
        return $user;
    }

    public function getRoleName($roles)
    {
        $role_name=[];
        foreach($roles as $role){
                array_push($role_name, $role['role']);
        }
        return $role_name;
    }
}
