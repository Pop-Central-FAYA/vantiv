<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Vanguard\Models\Company;

class CreateCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan command for creating a company';

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
        $name = $this->ask('What is the name of the company?');
        $parent_company_id = $this->ask('ID of the parent company');
        $channel_id = $this->ask('channel ID for publishers only');
        $company_type_id = $this->ask('Company type ID');
        try{
            $company = new Company();
            $company->name = $name;
            $company->parent_company_id = $parent_company_id;
            $company->address = '';
            $company->logo = '';
            $company->company_type_id = $company_type_id;
            $company->save();

            $company->channels()->attach($channel_id);

            $this->info($name.' was added successfully');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
