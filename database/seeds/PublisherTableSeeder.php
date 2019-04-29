<?php

use Illuminate\Database\Seeder;

use \Vanguard\Models\Company;
use \Vanguard\Models\Publisher;


class PublisherTableSeeder extends Seeder
{

    public function run()
    {

        Publisher::truncate();
    
        $companies = Company::all();
        foreach ($companies as $company) {
            if ($company->company_type->name == "broadcaster") {
                $type = "tv";
                if ($company->name == "SoundCity TV") {
                    $type = "radio";
                }
                Publisher::create(array("company_id" => $company->id, "type" => $type));
            }
        }
    }

   
}

// php artisan db:seed --class=PublisherTableSeeder