<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\CampaignDetail::class, function (Faker $faker) {
    return [
        'brand' => factory(\Vanguard\Models\Brand::class)->create()->id,
        'name' => $faker->name,
        'product' => $faker->title,
        'channel' => "'nzrm6hchjats36'",
        'start_date' => '2019-02-19 00:00:00',
        'stop_date' => '2019-02-28 00:00:00',
        'time_created' => '2019-02-07 14:44:32',
        'time_modified' => '2019-02-07 14:44:32',
        'status' => 'active',
        'day_parts' => "'nzrm6hchjatseog10','nzrm6hchjatseog5','nzrm6hchjatseog8','nzrm6hchjatseog9'",
        'target_audience' => "'nzrm6hchjatseog9'",
        'min_age' => 10,
        'max_age' => 60,
        'industry' => 'Technology Hardware, Storage & Peripherals',
        'sub_industry' => 'Technology Hardware, Storage & Peripherals',
        'adslots' => 5,
        'region' => "'naem6hqwjhjatseog8'",
        'walkins_id' => factory(\Vanguard\Models\WalkIns::class)->create()->id,
        'adslots_id' => "'5afabcc7d3105','5afabcd4b0694','5afabce15629a','5afabceef1497','5afabcff10abe'",
        'campaign_id' => '5c5c36408536f',
    ];
});
