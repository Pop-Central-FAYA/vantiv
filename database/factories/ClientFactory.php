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

$factory->define(Vanguard\Models\Client::class, function (Faker $faker) {
    return [
        'id' => uniqid(),
        'name' => $faker->company,
        //'brand' => uniqid(),
        'image_url' => $faker->url,
        'created_by' => uniqid(),
        'company_id' => uniqid(),
        'street_address' => $faker->address,
        'city' => $faker->city,
        'state' => $faker->state,
        'nationality' => $faker->country,
    ];
});
