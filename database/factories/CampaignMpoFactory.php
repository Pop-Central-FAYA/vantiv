<?php

use Faker\Generator as Faker;
use Vanguard\Models\Campaign;
use Vanguard\Models\CampaignMpo;

$factory->define(CampaignMpo::class, function (Faker $faker) {
    return [
        'campaign_id' => factory(Campaign::class)->create()->id,
        'adslots' => json_encode($faker->words),
        'status' => 'active',
        'ad_vendor_id' => uniqid(),
    ];
});
