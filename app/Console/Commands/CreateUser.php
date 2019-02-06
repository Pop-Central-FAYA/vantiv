<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\User\AuthenticatableUser;
use Vanguard\Services\User\CreateUser as AddNewUser;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command create user with basic information';

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
        $firstname = $this->ask('First Name');
        $lastname = $this->ask('Last Name');
        $email = $this->ask('Email');
        $password = $this->ask('Password');
        $company = $this->ask('Enter company/companies seperated with comma');
        $company_exploded = explode(',', $company);
        try{
            $create_authenticatable_user_service = new AuthenticatableUser($firstname, $lastname, $email, '',
                '', $password, '', $company_exploded);
            $user = $create_authenticatable_user_service->createAuthenticatableUser();
            $this->info($user->firstname.' was added successfully');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
