<?php

namespace Tests\RateCardTrait;

use Faker\Factory;

trait FakeRateCard
{
    public function getFakeRateCard()
    {
        $faker = Factory::create();
        $company_id = uniqid();
        $rate_card_type_id = uniqid();
        $duration = $faker->numberBetween(10, 50);
        $price = $faker->numberBetween(100000, 100000000);
        $start_time = $faker->time('H:i:s', 'now');
        $end_time = $faker->time('H:i:s', 'tomorrow');
        $title = $faker->title;
        $start_date = $faker->date('Y-m-d', 'now');
        $end_date = $faker->date('Y-m-d', 'tomorrow');
        return ['company_id' => $company_id, 'duration' => $duration, 'price' => $price, 'start_time' => $start_time,
            'end_time' => $end_time, 'title' => $title, 'rate_card_type_id' => $rate_card_type_id,
            'start_date' => $start_date, 'end_date' => $end_date];
    }
}

