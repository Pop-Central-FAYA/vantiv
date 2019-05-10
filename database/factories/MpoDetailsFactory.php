<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\MpoDetail::class, function (Faker $faker) {
    return [
        'mpo_id' => factory(\Vanguard\Models\Mpo::class)->create()->id,
        'is_mpo_accepted' => 1,
        'time_created' => '2019-02-07 13:44:33',
        'time_modified' => '2019-02-07 13:44:33',
        'status' => 1
    ];
});
