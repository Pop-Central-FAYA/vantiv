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

$factory->define(Vanguard\Models\ClientContact::class, function (Faker $faker) {
    return [
        'id' => uniqid(),
        'client_id' => uniqid(),
        'first_name' =>  $faker->firstName(),
        'last_name' =>  $faker->lastName,
        'email' => $faker->safeEmail,
        'phone_number' => $faker->phoneNumber,
        'is_primary' => true,
    ];
});
