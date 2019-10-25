<?php

use Faker\Generator as Faker;
use Vanguard\Models\MediaPlanProgram;

$factory->define(MediaPlanProgram::class, function (Faker $faker) {
    return [
        'program_name' => $faker->word,
        'station_id' => uniqid(),
        'attributes' => json_encode([
            'day' => $faker->dayOfWeek,
            'program_time' => $faker->time(),
            'rates' => [
                '15' => $faker->numberBetween(1000, 100000),
                '30' => $faker->numberBetween(1000, 100000),
                '45' => $faker->numberBetween(1000, 100000),
                '60' => $faker->numberBetween(1000, 100000)
            ],
            'time_belts' => [
                [
                    'start_time' => '09:00',
                    'end_time' => '09:15'
                ]
            ]
        ])
    ];
});
