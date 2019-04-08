<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\TimeBeltSelectedSlots::class, function (Faker $faker) {
    return [
        'campaign_id' => uniqid(),
        'time_belt_id' => factory(\Vanguard\Models\TimeBelt::class)->create()->id,
        'file_name' => $faker->name,
        'file_url' => $faker->url,
        'file_duration' => $faker->numberBetween(5,60),
        'file_format' => $faker->fileExtension,
        'amount_paid' => $faker->numberBetween(100000, 1000000000),
        'launched_on' => uniqid(),
        'created_by' => uniqid(),
        'belongs_to' => uniqid()
    ];
});
