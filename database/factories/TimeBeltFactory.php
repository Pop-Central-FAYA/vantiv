<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\TimeBelt::class, function (Faker $faker) {
    return [
        'start_time' => $faker->time('H:i:s', 'now'),
        'end_time' => $faker->time('H:i:s', 'now'),
        'day' => $faker->dayOfWeek,
        'media_program_id' => factory(\Vanguard\Models\MediaProgram::class)->create()->id,
        'station_id' => factory(\Vanguard\Models\Company::class)->create()->id
    ];
});
