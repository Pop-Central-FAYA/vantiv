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

$factory->define(Vanguard\Model\Client::class, function (Faker $faker) {
    return [
        'id' => $faker->word,
        'name' => $faker->word,
        'brand' => $faker->word,
        'image_url' => $faker->url,
        'status' => $faker->word,
        'created_by' => $faker->word,
        'company_id' => $faker->word,
        'street_address' => $faker->word,
        'city' => $faker->word,
        'state' => $faker->word,
        'nationality' => $faker->word,
    ];
});
