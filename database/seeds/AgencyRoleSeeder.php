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

       $vantage_permissions = [
        'create.media_plan', 'update.media_plan', 'submit.media_plan', 'view.media_plan', 
        'view.invoice', 'view.wallet', 'create.wallet', 'create.asset', 'view.asset', 'update.asset'
       ];

       //fetch all the permissions
       $permissions = Permission::where('guard_name', 'web')->whereIn('name', $vantage_permissions)->get();

       //sync permissions to the admin role
       $admin_role->syncPermissions($permissions);

    }
}
