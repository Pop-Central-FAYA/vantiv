<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\Mpo::class, function (Faker $faker) {
    return [
        'campaign_id' => '5c5c36408536f',
        'campaign_reference' => '32629068',
        'time_created' => '2019-02-07 13:44:32',
        'time_modified' => '2019-02-07 13:44:32'
    ];
});
