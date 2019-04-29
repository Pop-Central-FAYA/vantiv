<?php

use Illuminate\Database\Seeder;

use \Vanguard\Models\FakeTimeBeltRevenue;
use \Vanguard\Models\TimeBelt;

class FakeTimeBeltRevenueSeeder extends Seeder
{
    const RANGE_VALUES= array(
        array("min" => 0, "max" => 1000),
        array("min" => 1001, "max" => 20000),
        array("min" => 20001, "max" => 50000),
        array("min" => 50001, "max" => 500000),
        array("min" => 500001, "max" => 2000000),
        array("min" => 2000001, "max" => 5000000)
    );
    /**
     * Run the database seeds.
     * This method will do the following:
     * 1. clear all seeded data
     * 2. read all data from the stations table
     * 3. in a database transaction generate and save data for each timebelt combination between a certain range
     * @return void
     */
    public function run()
    {
        FakeTimeBeltRevenue::truncate();
        $all_timebelts = Timebelt::all();
        foreach ($all_timebelts as $timebelt) {
            FakeTimeBeltRevenue::create([
                'station_id' => $timebelt->station_id,
                'day' => $timebelt->day,
                'start_time' => $timebelt->start_time,
                'end_time' => $timebelt->end_time,
                'revenue' => $this->generateRandomRevenue()
            ]);
        }
    }

    protected function generateRandomRevenue() {
        $range = (int) rand(0, 5);
        $min_max = static::RANGE_VALUES[$range];
        return (int) rand($min_max["min"], $min_max["max"]);
    }
}