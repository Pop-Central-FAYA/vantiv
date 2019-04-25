<?php

namespace Vanguard\Services\RolesPermission;

use Spatie\Permission\Models\Role;

class ListRoleGroup
{
    protected $group_name;

    public function __construct($group_name)
    {
        $this->group_name = $group_name;
    }

    private function getRolesQuery()
    {
        return Role::where('guard_name', $this->group_name)->get();
    }

    public function getRoles()
    {
        $roles = [];
        foreach ($this->getRolesQuery() as $role){
            $roles[] = $this->rolesArray($role);
        }
        return $roles;
    }

    private function rolesArray($role)
    {
        return [
            'id' => $role->id,
            'role' => $role->name,
            'label' => ucwords(str_replace('_', ' ', explode('.', $role->name)[1]))
        ];
    }
}
