<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\Publisher::class, function (Faker $faker) {
    return [
        'company_id' => factory(\Vanguard\Models\Company::class)->create()->id,
        'type' => 'tv'
    ];
});
