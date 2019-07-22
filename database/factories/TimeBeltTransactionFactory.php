<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\TimeBeltTransaction::class, function (Faker $faker) {
    $program = factory(\Vanguard\Models\MediaProgram::class)->create();
    return [
        'time_belt_id' => factory(\Vanguard\Models\TimeBelt::class)->create()->id,
        'media_program_id' => $program->id,
        'media_program_name' => $program->name,
        'company_id' => factory(\Vanguard\Models\Company::class)->create()->id,
        'playout_date' => $faker->date('Y-m-d', 'now'),
        'duration' => $faker->numberBetween(10, 90),
        'file_name' => $faker->name,
        'file_url' => $faker->url,
        'campaign_details_id' => 'jmfcndbnfcv',
        'file_format' => $faker->fileExtension,
        'amount_paid' => $faker->numberBetween(100000, 10000000),
        'playout_hour' => $faker->time('H:i:s', 'now'),
        'approval_status' => 'approved',
        'payment_status' => 'approved',
        'order' => 0
    ];
});
