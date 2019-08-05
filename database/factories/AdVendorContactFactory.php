<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\AdVendorContact::class, function (Faker $faker) {
    return [
        'ad_vendor_id' => uniqid(),
        'first_name' => $faker->firstName(),
        'last_name' => $faker->lastName,
        'email' => $faker->safeEmail,
        'phone_number' => $faker->phoneNumber,
        'is_primary' => true,
        'created_by' => uniqid()
    ];
});