<?php

use Faker\Generator as Faker;
use Vanguard\Models\Ratecard\Ratecard;

$factory->define(Ratecard::class, function (Faker $faker) {
    return [
        'title' => $title = $faker->title,
        'company_id' => factory(\Vanguard\Models\Company::class)->create()->id,
        'slug' => str_slug($title),
        'status' => \Vanguard\Libraries\Enum\ProgramStatus::ACTIVE
    ];
});
