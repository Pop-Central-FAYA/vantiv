<?php

use Faker\Generator as Faker;
use Vanguard\Models\CampaignMpo;
use Vanguard\Models\CampaignMpoTimeBelt;

$factory->define(CampaignMpoTimeBelt::class, function (Faker $faker) {
    return [
        'mpo_id' => factory(CampaignMpo::class)->create()->id,
        'time_belt_start_time' => '11:00:00',
        'time_belt_end_date' => '11:15:00',
        'day' => 'Monday',
        'duration' => 15,
        'program' => 'Super Story',
        'ad_slots' => 5,
        'playout_date' => '2019-07-12',
        'asset_id' => uniqid(),
        'volume_discount' => 5,
        'net_total' => 19000,
        'unit_rate' => 4000
    ];
});
