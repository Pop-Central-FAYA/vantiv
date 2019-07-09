<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\TimeBelt::class, function (Faker $faker) {
    return [
        'start_time' => '00:00',
        'end_time' => '00:15',
        'day' => $faker->dayOfWeek,
        'media_program_id' => factory(\Vanguard\Models\MediaProgram::class)->create()->id,
        'station_id' => factory(\Vanguard\Models\Company::class)->create()->id
    ];
});
