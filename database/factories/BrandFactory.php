<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\Brand::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->name,
        'image_url' => $faker->imageUrl(),
        'industry_code' => 452020,
        'sub_industry_code' => 45202030
    ];
});
