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

$factory->define(Vanguard\Models\Invoice::class, function (Faker $faker) {
    return [
        'id' => uniqid(),
        'campaign_id' => uniqid(),
        'campaign_reference' => $faker->ean8,
        'invoice_number' => $faker->ean8,
        'time_created' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'time_modified' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'status' => 'active',
        'payment_id' => uniqid(),
    ];
});
