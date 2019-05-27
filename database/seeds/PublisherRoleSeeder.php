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
        //delete role record
        DB::table('roles')->delete();

        //create a default admin role
        $admin_role = Role::create([
                    'name' => 'admin',
                    'guard_name' => 'ssp'
                ]);

        //create a default suoer admin role
        $super_admin_role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'ssp'
        ]);

        //fetch all the permissions for publishers
        $permissions = Permission::where('guard_name', 'ssp')->get();

        //sync permissions to the admin role
        $admin_role->syncPermissions($permissions->where('name', '<>', 'edit.super_admin'));

        //sync permission to super admin role
        $super_admin_role->syncPermissions($permissions);
    }
}
