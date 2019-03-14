<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Vanguard\Services\RolesPermission\StoreRoleService;

class CreateRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates roles in the system';

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
        $role = $this->ask('What role do you want to create ?');
        try{
            $store_role_service = new StoreRoleService(strtolower($role));
            $store_role_service->storeRoles();
            $this->info($role.' was added successfully');
        }catch(\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
