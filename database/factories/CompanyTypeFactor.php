<?php

use Faker\Generator as Faker;
use Vanguard\Models\CompanyType;

$factory->define(CompanyType::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name
    ];
});
