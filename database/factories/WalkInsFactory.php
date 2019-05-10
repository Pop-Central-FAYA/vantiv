<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\WalkIns::class, function (Faker $faker) {
    return [
        'user_id' => factory(\Vanguard\User::class)->create()->id,
        'location' => $faker->streetAddress,
        'company_logo' => 'http://jhgddvxagsb.com/jh,gdhs'
    ];
});
