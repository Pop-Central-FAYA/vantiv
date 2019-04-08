<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Services\Company\CompanyDetails;

class PopulateTimeBelt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:time-belt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command helps populate the time belt for each publisher just by supplying the publisher id';

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
        $publisher_id = $this->ask('What is your publisher id ? ');
        //check publisher
        $company_service = new CompanyDetails($publisher_id);
        $company_details = $company_service->getCompanyDetails();
        if($company_details && $company_details->company_type->name == CompanyTypeName::BROADCASTER){
            $populate_time_belt_service = new \Vanguard\Services\Inventory\PopulateTimeBelt($publisher_id);
            $populate_time_belt = $populate_time_belt_service->populateTimeBelt();
            if($populate_time_belt == 'success'){
                $this->info('Your inventory has been populated');
            }else{
                $this->error('Time belt already exist for this publisher');
            }
        }else{
            $this->error('An error occurred when populating the time belts');
        }
    }
}
