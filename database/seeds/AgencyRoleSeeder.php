<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AgencyRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Build according to: https://docs.spatie.be/laravel-permission/v2/advanced-usage/seeding/
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // php artisan db:seed --class=AgencyRoleSeeder
        $vantagePermissions = [
            'create.media_plan' => array('dsp.admin', 'dsp.media_planner'),
            'update.media_plan' => array('dsp.admin', 'dsp.media_planner'),
            'submit.media_plan' => array('dsp.admin', 'dsp.media_planner'),
            'view.media_plan' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'approve.media_plan' => array('dsp.finance', 'dsp.admin'),
            'decline.media_plan' => array('dsp.finance', 'dsp.admin'),
            'export.media_plan' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'convert.media_plan' => array('dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'delete.media_plan' => array('dsp.admin', 'dsp.media_planner'),

            'view.invoice' => array('dsp.finance', 'dsp.admin', 'dsp.media_buyer'),

            'view.wallet' => array('dsp.finance', 'dsp.admin', 'dsp.media_buyer'),
            'create.wallet' => array('dsp.finance', 'dsp.admin', 'dsp.media_buyer'),

            'create.asset' => array('dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'view.asset' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'update.asset' => array('dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'delete.asset' => array('dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),

            'create.user' => array('dsp.admin'),
            'view.user' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'update.user' => array('dsp.admin'),

            'view.campaign' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'submit.campaign' => array('dsp.admin', 'dsp.media_buyer'),
            'create.campaign' => array('dsp.admin', 'dsp.media_buyer'),
            'update.campaign' => array('dsp.admin', 'dsp.media_buyer'),

            'view.profile' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'update.profile' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),

            'view.report' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),

            'view.client' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'update.client' => array('dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'create.client' => array('dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),

            'view.ad_vendor' => array('dsp.compliance', 'dsp.finance', 'dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'update.ad_vendor' => array('dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),
            'create.ad_vendor' => array('dsp.admin', 'dsp.media_planner', 'dsp.media_buyer'),

            'update.company' => array('dsp.admin')
        ];
        
        //Create the different roles if they do not exist
        foreach ($this->getRoles($vantagePermissions) as $roleName) {
            $role = Role::firstOrNew(['name' => $roleName]);
            $role->name = $roleName;
            $role->guard_name = 'web';
            $role->save();
        }

        //Create all the different permissions if they do not exist already
        foreach ($this->getPermissions($vantagePermissions) as $permissionName) {
            $permission = Permission::firstOrNew(['name' => $permissionName, 'guard_name' => 'web']);
            $permission->name = $permissionName;
            $permission->guard_name = 'web';
            $permission->save();
        }

        //sync the permissions to roles
        foreach ($this->groupPermissionsByRoles($vantagePermissions) as $roleName => $permissionList) {
            $role = Role::where(['name' => $roleName, 'guard_name' => 'web'])->first();
            $permissions = Permission::where('guard_name', 'web')->whereIn('name', $permissionList)->get();
            $role->syncPermissions($permissions);
        }
    }
    
    private function getRoles($permissionList)
    {
        $roleList = collect([]);
        foreach ($permissionList as $permission => $allowedRoles) {
            $roleList = $roleList->concat($allowedRoles);
        }
        return $roleList->unique()->values()->all();
    }

    private function getPermissions($permissionList)
    {
        return collect($permissionList)->keys()->all();
    }

    private function groupPermissionsByRoles($permissionList)
    {
        $finalGroup = [];

        foreach ($this->getRoles($permissionList) as $roleName) {
            $finalGroup[$roleName] = collect([]);
        }

        foreach ($permissionList as $permission => $allowedRoles) {
            foreach ($allowedRoles as $roleName) {
                $rolePerms = $finalGroup[$roleName];
                $finalGroup[$roleName] = $rolePerms->push($permission);
            }
        }

        //make unique
        foreach ($finalGroup as $roleName => $perms) {
            $finalGroup[$roleName] = $perms->unique()->values()->all();
        }

        return $finalGroup;
    }
}
