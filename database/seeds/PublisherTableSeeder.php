<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use \Vanguard\Models\Publisher;
use Vanguard\Libraries\Station\Reader as StationReader;

class PublisherTableSeeder extends Seeder
{

    public function run()
    {

        DB::transaction(function() {
            foreach (StationReader::getTvStationList() as $station) {
                $short_name = $station['publisher'];
                $full_name = Arr::get($station, 'publisher_full', $short_name);
                $type = 'tv';

                $unique_attrs = ['name' => $short_name, 'type' => $type];
                $attrs = ['settings' => json_encode([]), 'long_name' => $full_name];
                Publisher::updateOrCreate($unique_attrs, $attrs);
            }
        });
        
    }
   
}
// php artisan db:seed --class=PublisherTableSeeder