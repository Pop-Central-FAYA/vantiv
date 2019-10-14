<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Vanguard\Models\TvStation;
use Vanguard\Models\Publisher;
use Vanguard\Libraries\Station\Reader as StationReader;
/**
 * This will loop through and create a tv station and associate with a publisher
 * (If that tv station and publisher is not created yet)
 */
class TvStationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::transaction(function() {
            $publishers = $this->getTvStationPublishers();
            $stations = StationReader::getTvStationList();
            $this->create($stations, $publishers);
        });
    }

    protected function create($station_list, $publishers) {
        foreach ($station_list as $station) {
            try {
                $name = $station['name'];
                $type = $station['type'];
                $state = Arr::get($station, 'state', '');
                $city = Arr::get($station, 'city', '');
                $region = Arr::get($station, 'region', '');
                $publisher_name = $station['publisher'];

                $key = md5("{$name}-{$type}-{$state}-{$city}-{$region}");
                $station_attrs = array(
                    'publisher_id' => $publishers[$publisher_name][0]['id'],
                    'name' => $name,
                    'type' => $type,
                    'state' => $state,
                    'city' => $city,
                    'region' => $region,
                    'broadcast' => $station['broadcast']
                );
                TvStation::updateOrCreate(['key' => $key], $station_attrs);
            } catch (\Throwable $th) {
                var_dump($station);
                throw $th;
            }
            
        }
    }

    protected function getTvStationPublishers()
    {
        $publishers = Publisher::select('name', 'long_name', 'id')->where('type', 'tv')->get();
        return $publishers->groupBy('name');
    }
}
// php artisan db:seed --class=TvStationTableSeeder
