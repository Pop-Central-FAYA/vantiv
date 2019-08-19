<?php

use Faker\Generator as Faker;
use Vanguard\Models\MpoShareLink;
use Vanguard\Models\CampaignMpo;
use Carbon\Carbon;

$factory->define(MpoShareLink::class, function (Faker $faker) {
    $mpo = factory(CampaignMpo::class)->create();
    return [
        'id' => $id = uniqid(),
        'mpo_id' => $mpo->id,
        'email' => 'test@gmail.com',
        'url' => URL::signedRoute('guest.mpo_share_link', ['id' => $id]),
        'expired_at' => Carbon::parse($mpo->campaign->stop_date)->addDays(90),
    ];
});
