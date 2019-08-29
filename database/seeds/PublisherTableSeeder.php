<?php

use Illuminate\Database\Seeder;

use \Vanguard\Models\Company;
use \Vanguard\Models\Publisher;
use \Vanguard\Models\TvStation;


class PublisherTableSeeder extends Seeder
{

    public function run()
    {

        // Publisher::truncate();
        // $companies = Company::all();
        // foreach ($companies as $company) {
        //     if ($company->company_type->name == "broadcaster") {
        //         $type = "tv";
        //         if ($company->name == "SoundCity TV") {
        //             $type = "radio";
        //         }
        //         Publisher::create(array("company_id" => $company->id, "type" => $type));
        //     }
        // }
        
        // Seed the tv station table (Right now we will create a one to one mapping of publisher and tv station)
        $this->call(TvStationTableSeeder::class);
        $all_stations = TvStation::all();
        foreach ($all_stations as $station) {
            $publisher_attrs = array('name' => $station->name, 'type' => 'tv');
            $publisher = Publisher::firstOrCreate($publisher_attrs, ['settings' => json_encode([])]);
            $station->publisher_id = $publisher->id;
            $station->save();
        }
    }

   
}
// php artisan db:seed --class=PublisherTableSeeder