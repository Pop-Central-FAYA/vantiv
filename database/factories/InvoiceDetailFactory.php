<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Vanguard\Models\InvoiceDetail::class, function (Faker $faker) {
    return [
        'invoice_id' => uniqid(),
        'user_id' => uniqid(),
        'broadcaster_id' => uniqid(),
        'invoice_number' => $faker->ean8,
        'actual_amount_paid' => $faker->numberBetween(1000, 9000),
        'refunded_amount' => $faker->numberBetween(1000, 9000),
        'time_created' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'time_modified' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'status' => 'active',
        'walkins_id' => uniqid(),
        'agency_id' => uniqid(),
    ];
});
