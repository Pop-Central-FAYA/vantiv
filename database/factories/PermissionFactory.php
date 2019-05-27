<?php

use Faker\Generator as Faker;

$factory->define(\Spatie\Permission\Models\Permission::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'guard_name' => 'ssp'
    ];
});
