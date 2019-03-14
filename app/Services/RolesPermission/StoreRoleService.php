<?php

namespace Vanguard\Services\RolesPermission;

use Spatie\Permission\Models\Role;

class StoreRoleService
{
    protected $role_name;

    public function __construct($role_name)
    {
        $this->role_name = $role_name;
    }

    public function storeRoles()
    {
        $roles = new Role();
        $roles->name = $this->role_name;
        $roles->save();
        return $roles;
    }
}
