<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Matrix\Exception;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AttachPermissionsToRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attach:permissions-to-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command helps the engineers to attach permissions to a role from the backend';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $role_name = $this->ask('Please enter the name of the role');
        $guard = $this->ask('Please enter the guard');
        $permissions = $this->ask('Please enter the permissions you want to assign to this role separated with (,)');
        try{
            $role = Role::findByName($role_name, $guard);
            $exploded_permissions = explode(',', $permissions);
            $permission_array = [];
            foreach ($exploded_permissions as $permission){
                try{
                    $get_permission = Permission::findByName($permission, 'ssp');
                    array_push($permission_array, $get_permission);
                }catch (Exception $e){
                    $this->error($e->getMessage());
                }
            }
            $role->syncPermissions($permission_array);
            $this->info('Permissions attached to role successfully');
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
    }
}
