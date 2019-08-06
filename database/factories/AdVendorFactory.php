<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\AdVendor::class, function (Faker $faker) {
    return [
        'company_id' => uniqid(),
        'name' => $faker->company,
        'street_address' => $faker->streetAddress(),
        'city' => $faker->city,
        'state' => $faker->state,
        'country' => $faker->country,
        'created_by' => uniqid()
    ];
});
