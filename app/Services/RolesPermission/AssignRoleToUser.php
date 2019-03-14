<?php

namespace Vanguard\Services\RolesPermission;

use Spatie\Permission\Models\Role;
use Vanguard\User;

class AssignRoleToUser
{
    protected $user_id;
    protected $role_id;

    public function __construct($user_id, $role_id)
    {
        $this->user_id = $user_id;
        $this->role_id = $role_id;
    }

    public function assignRolesToUser()
    {
        $user = User::where('id', $this->user_id)->first();
        $role = Role::where('id', $this->role_id)->first();
        if($user && $role){
            return $user->assignRole($role->name);
        }else{
            return 'error';
        }
    }
}
