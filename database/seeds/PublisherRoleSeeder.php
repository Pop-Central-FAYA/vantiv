<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PublisherRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create a default admin role
        $admin_role = Role::firstOrNew(['name' => 'ssp.admin']);
        $admin_role->name = 'ssp.admin';
        $admin_role->guard_name = 'web';
        $admin_role->save();

        //create a default suoer admin role
        $super_admin_role = Role::firstOrNew(['name' => 'ssp.super_admin']);
        $super_admin_role->name = 'ssp.super_admin';
        $super_admin_role->guard_name = 'web';
        $super_admin_role->save();

        //fetch all the permissions for publishers
        $permissions = Permission::where('guard_name', 'web')->get();

        //sync permissions to the admin role
        $admin_role->syncPermissions($permissions->where('name', '<>', 'update.super_admin'));

        //sync permission to super admin role
        $super_admin_role->syncPermissions($permissions);
    }
}
