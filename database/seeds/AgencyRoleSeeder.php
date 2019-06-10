<?php

use Illuminate\Database\Seeder;

class AgencyRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       //create a default admin role
       $admin_role = Role::firstOrNew(['name' => 'dsp.admin']);
       $admin_role->name = 'dsp.admin';
       $admin_role->guard_name = 'web';
       $admin_role->save();

       //create a default suoer admin role
       $super_admin_role = Role::firstOrNew(['name' => 'dsp.super_admin']);
       $super_admin_role->name = 'dsp.super_admin';
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
