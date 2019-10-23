<?php

use Faker\Generator as Faker;
use Vanguard\Models\Campaign;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Brand;
use Vanguard\Models\Client;
use Vanguard\User;

$factory->define(Campaign::class, function (Faker $faker) {
    return [
        'start_date' => '2019-07-03',
        'stop_date' => '2019-09-01',
        'campaign_reference' => Utilities::generateReference(),
        'budget' => 1000000,
        'ad_slots' => 5,
        'status' => 'active',
        'agency_commission' => 10,
        'product' => 'New Product',
        'name' => 'New Campaign',
        'regions' => json_encode(["North-West"]),
        'age_groups' => json_encode(["min"=>"18","max"=>"99"]),
        'target_audience' => json_encode(["nzrm6hchjatseog9","nzrm6hchjatseoga"]),
        'channel' => json_encode(["nzrm6hchjats36"]),
        'ad_slots' => 10,
        'lsm' => null,
        'social_class' => json_encode(["A","B","C","D","E"]),
        'states' => json_encode(["Abia","Abuja"]),
        'brand_id' => factory(Brand::class)->create()->id,
        'walkin_id' => factory(Client::class)->create()->id,
        'created_by' => factory(User::class)->create()->id,
        'belongs_to' => uniqid()
    ];
});
