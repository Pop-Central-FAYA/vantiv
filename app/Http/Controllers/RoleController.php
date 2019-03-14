<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Requests\Request;
use Vanguard\Services\RolesPermission\StoreRoleService;

class RoleController extends Controller
{
    public function storeRoles(Request $request)
    {
        $store_roles_service = new StoreRoleService($request->name);
        return $store_roles_service->storeRoles();
    }
}
