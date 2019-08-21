<?php

use Faker\Generator as Faker;
use Vanguard\Models\Campaign;
use Vanguard\Models\CampaignMpo;

$factory->define(CampaignMpo::class, function (Faker $faker) {
    return [
        'campaign_id' => factory(Campaign::class)->create()->id,
        'station' => 'jrhfhjsd',
        'ad_slots' => 6,
        'status' => 'active',
        'budget' => 40000
    ];
});
