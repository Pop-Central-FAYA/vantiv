<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\MediaProgram::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'status' => 'active'
    ];
});
