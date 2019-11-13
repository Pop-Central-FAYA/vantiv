<?php

use Faker\Generator as Faker;
use Vanguard\Models\Brand;
use Vanguard\Models\Client;
use Vanguard\Models\MediaAsset;

$factory->define(MediaAsset::class, function (Faker $faker) {
    return [
        'file_name' => 'New file.mp4',
        'client_id' => factory(Client::class)->create()->id,
        'brand_id' => factory(Brand::class)->create()->id,
        'media_type' => 'Tv',
        'asset_url' => $faker->url,
        'created_by' => uniqid(),
    ];
});
