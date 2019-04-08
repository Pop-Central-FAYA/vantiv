<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\Day::class, function (Faker $faker) {
    return [
        'day' => $faker->dayOfWeek()
    ];
});
