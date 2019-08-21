<?php

use Faker\Generator as Faker;
use Vanguard\Models\MpoShareLinkActivity;
use Vanguard\Models\MpoShareLink;

$factory->define(MpoShareLinkActivity::class, function (Faker $faker) {
    return [
        'ip_address' => '192.168.0.0',
        'description' => 'Hello World',
        'mpo_share_link_id' => factory(MpoShareLink::class)->create()->id,
        'user_agent' => 'Hi there'
    ];
});
