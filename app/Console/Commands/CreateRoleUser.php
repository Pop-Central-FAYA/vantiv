<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Vanguard\Services\RolesPermission\AssignRoleToUser;

class CreateRoleUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:role-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command attach a role to a user';

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
        $user_id = $this->ask('Please enter the user_id');
        $role_id = $this->ask('Please enter the role_id');
        $guard = $this->ask('Please enter the guard');
        try{
            $assign_roles_service = new AssignRoleToUser($user_id, $role_id, $guard);
            $assign_roles_service->assignRolesToUser();
            $this->info('role assigned to user successfully');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
