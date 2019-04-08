<?php

use Faker\Generator as Faker;
use Vanguard\Models\Ratecard\Ratecard;

$factory->define(Ratecard::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'company_id' => factory(\Vanguard\Models\Company::class)->create()->id,
        'duration' => $faker->numberBetween(10, 50),
        'price' => $faker->numberBetween(100000, 100000000),
        'start_time' => $faker->time('H:i:s', 'now'),
        'end_time' => $faker->time('H:i:s', 'tomorrow')
    ];
});
