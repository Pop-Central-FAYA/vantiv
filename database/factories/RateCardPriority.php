<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\RatecardPriority::class, function (Faker $faker) {
    return [
        'rate_card_type' => $faker->unique()->title,
        'priority' => $faker->unique()->numberBetween(1, 5)
    ];
});
