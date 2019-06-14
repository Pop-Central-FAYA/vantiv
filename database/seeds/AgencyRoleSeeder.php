<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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



       //fetch all the permissions
       $permissions = Permission::where('guard_name', 'web')->get();

       //sync permissions to the admin role
       $admin_role->syncPermissions($permissions->where('name', '<>', 'update.super_admin'));

    }
}
