<?php

namespace Vanguard\Services\RolesPermission;

use Spatie\Permission\Models\Role;

class StoreRoleService
{
    protected $role_name;
    protected $guard_name;

    public function __construct($role_name, $guard_name)
    {
        $this->role_name = $role_name;
        $this->guard_name = $guard_name;
    }

    public function storeRoles()
    {
        $roles = new Role();
        $roles->name = $this->role_name;
        $roles->guard_name = $this->guard_name;
        $roles->save();
        return $roles;
    }
}
