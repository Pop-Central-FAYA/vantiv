<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Carbon;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command takes in group of permissions and the guard';

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
        $permissions = $this->ask('What permission/permissions do you want to create ? for multiple please seperate with comma');
        $guard_name = $this->ask('Please specify the guard');
        $exploded_permissions = explode(',', $permissions);
        $permission_array = [];
        foreach ($exploded_permissions as $permission){
            $permission_array[] = [
                'name' => trim($permission),
                'guard_name' => $guard_name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        try{
            Permission::insert($permission_array);
            $this->info('Permissions were added successfully');
        }catch(\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
