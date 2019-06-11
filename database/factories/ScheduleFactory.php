<?php

use Faker\Generator as Faker;

$factory->define(\Vanguard\Models\Schedule::class, function (Faker $faker) {
    return [
        'company_id' => factory(\Vanguard\Models\Company::class)->create()->id,
        'playout_date' => '2019-05-4',
        'playout_hour' => '11:00:00',
        'duration' => 30,
        'file_name' => 'this.mp4',
        'file_url' => 'http://hgdbwrgehsbx.hjgf/kjfd.mp4',
        'ad_break' => '11:00:00'
    ];
});
