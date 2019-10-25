<?php

use Faker\Generator as Faker;
use Vanguard\Models\Publisher;
use Vanguard\Models\TvStation;

$factory->define(TvStation::class, function (Faker $faker) {
    return [
        'publisher_id' => factory(Publisher::class)->create()->id,
        'name' => $faker->company,
        'type' => $faker->word,
        'state' => $faker->country,
        'city' => $faker->city,
        'region' => $faker->city,
        'key' => uniqid(),
        'broadcast' => $faker->word
    ];
});
