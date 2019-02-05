<?php

use Faker\Generator as Faker;
use Vanguard\Models\Company;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->company,
        'address' => $faker->address,
        'parent_company_id' => $faker->uuid,
        'company_type_id' => $faker->uuid
    ];
});
