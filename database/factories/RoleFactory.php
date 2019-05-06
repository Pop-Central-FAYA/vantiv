<?php

use Faker\Generator as Faker;

$factory->define(\Spatie\Permission\Models\Role::class, function (Faker $faker) {
    return [
        'name' => 'ssp.admin',
        'guard_name' => 'ssp'
    ];
});
