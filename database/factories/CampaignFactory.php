<?php

use Faker\Generator as Faker;
use Vanguard\Models\Campaign;
use Vanguard\Models\Company;
use Vanguard\Libraries\Utilities;

$factory->define(Campaign::class, function (Faker $faker) {
    return [
        'start_date' => '2019-07-03',
        'stop_date' => '2019-09-01',
        'campaign_reference' => Utilities::generateReference(),
        'budget' => 1000000,
        'ad_slots' => 5,
        'status' => 'active',
        'agency_commission' => 10,
        'belongs_to' => factory(Company::class)->create()->id
    ];
});
