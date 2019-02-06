<?php

use Faker\Generator as Faker;
use Vanguard\Models\ParentCompany;

$factory->define(ParentCompany::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->company
    ];
});
