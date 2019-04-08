<?php

namespace Tests\Feature\RateCard;

use Tests\TestCase;
use Vanguard\Libraries\Enum\RateCardTypes;
use Vanguard\Models\Ratecard\Ratecard;
use Vanguard\Models\RatecardPriority;
use Vanguard\Services\RateCard\GetRatecardRange;
use Vanguard\Services\RateCard\GetRateCards;
use Vanguard\Services\RateCard\GetSpecificRateCard;
use Vanguard\Services\RateCard\GetSpecificRateCardFromAdslotLists;

class GetRateCardServiceTest extends TestCase
{
    use \Tests\RateCardTrait\FakeRateCard;

    public function test_it_can_get_rate_cards_when_given_file_duration_and_play_time()
    {
        //create a rate_card
        $time = $this->getStartAndEndTime();
        $playout_hour = date('H:i:s', strtotime('12:00'));
        $faker = $this->getFakeRateCard();
        $company_id = uniqid();
        $base_rate_card = $this->createBaseRateCard($time, $faker['duration'], $company_id);
        $get_rate_card_service = new GetRateCards($faker['duration'], $playout_hour);
        $get_rate_card = $get_rate_card_service->getRateCards();

        $this->assertEquals($base_rate_card->price, $get_rate_card[0]->price);
    }

    public function test_it_can_return_rate_card_for_different_publishers_when_given_file_duration_and_play_time()
    {
        $time = $this->getStartAndEndTime();
        $playout_hour = date('H:i:s', strtotime('12:00'));
        $faker = $this->getFakeRateCard();
        $company_1 = uniqid();
        $company_2 = uniqid();
        $this->createBaseRateCard($time, $faker['duration'], $company_1);
        $this->createBaseRateCard($time, $faker['duration'], $company_2);

        $get_rate_card_service = new GetRateCards($faker['duration'], $playout_hour);
        $get_rate_card = $get_rate_card_service->groupRateCardsByPublishers();

        $this->assertEquals($company_1, $get_rate_card[$company_1][0]->company_id);
        $this->assertEquals($company_2, $get_rate_card[$company_2][0]->company_id);
    }

    public function test_it_can_get_rate_cards_when_given_adslots()
    {
        //create the ratecards
        $time = $this->getStartAndEndTime();
        $company_id = uniqid();
        $this->createRateCardRange($time, $company_id);
        //adslot_data
        $adslots = $this->getAdslots();

        $get_rate_card_range_service = new GetRatecardRange($adslots);
        $get_ratecard_range = $get_rate_card_range_service->getRatecardRange();

        $this->assertArrayHasKey('file_duration', $get_ratecard_range[0]);
    }

    public function test_it_can_get_adslot_grouped_by_publishers_when_given_adslots()
    {
        $adslots = $this->getAdslots();
        $time = $this->getStartAndEndTime();
        $company_id_1 = uniqid();
        $company_id_2 = uniqid();
        $this->createRateCardRange($time, $company_id_1);
        $this->createRateCardRange($time, $company_id_2);

        $get_rate_card_range_service = new GetRatecardRange($adslots);
        $get_ratecard_range = $get_rate_card_range_service->getRangeGroupByPublishers();
        $this->assertEquals($company_id_1, $get_ratecard_range[0]['rate_card'][0][$company_id_1][0]->company_id);
        $this->assertEquals($company_id_2, $get_ratecard_range[0]['rate_card'][0][$company_id_2][0]->company_id);
    }

    public function test_it_actually_returns_the_correct_rate_card()
    {
        //create the ratecards
        $time = $this->getStartAndEndTime();
        //create factory
        \factory(Ratecard::class, 2)->create([
            'start_time' => $time['start_time'],
            'end_time' => $time['end_time'],
            'duration' => 15
        ]);

        \factory(Ratecard::class, 2)->create([
            'start_time' => $time['start_time'],
            'end_time' => $time['end_time'],
            'duration' => 30
        ]);

        //adslot_data
        $adslots = [
            [
                'file_duration' => 15,
                'time_hour' => '12:00:00'
            ],[
                'file_duration' => 30,
                'time_hour' => '13:00:00'
            ]
        ];

        $get_rate_card_range_service = new GetRatecardRange($adslots);
        $get_ratecard_range = $get_rate_card_range_service->getRatecardRange();

        $this->assertEquals(2, count($get_ratecard_range[0]['rate_card'][0]));
    }

    public function test_it_can_get_specific_rate_card()
    {
        //create a specific rate card
        $specific_rate_card = factory(Ratecard::class)->create([
                                    'ratecard_type' => RateCardTypes::AGENCY,
                                    'ratecard_type_id' => uniqid()
                                ]);

        $this->assertEquals(RateCardTypes::AGENCY, $specific_rate_card->ratecard_type);

    }

    public function test_it_can_sort_specific_rate_cards_by_priority()
    {
        //create a specific rate card for agency
        $this->createPriorityRateCards($this->rateCardTypeIds(), $this->getStartAndEndTime(), $this->createPriority(), uniqid());

        $specific_rate_card_service = new GetSpecificRateCard(15, '12:00:00', $this->rateCardTypeIds(), '2019-03-25', '2019-03-28');
        $specific_rates = $specific_rate_card_service->getRateCards();

        $this->assertGreaterThan($specific_rates[0]->priority, $specific_rates[1]->priority);
    }

    public function test_it_can_get_specific_rates_from_adslot_list()
    {
        $adslots = [
            [
                'file_duration' => 15,
                'time_hour' => '12:00:00'
            ],[
                'file_duration' => 30,
                'time_hour' => '13:00:00'
            ]
        ];

        $company_id = uniqid();

        $this->createPriorityRateCards($this->rateCardTypeIds(), $this->getStartAndEndTime(), $this->createPriority(), $company_id);

        $get_specific_range_from_adslot_service_service = new GetSpecificRateCardFromAdslotLists($this->rateCardTypeIds(), '2019-03-25', '2019-03-28', $adslots);
        $get_specific_range_from_adslot_service = $get_specific_range_from_adslot_service_service->getRateFromAdslotList();

        $this->assertEquals(3, count($get_specific_range_from_adslot_service[0]['rate_card'][0]));
    }

    public function test_it_can_get_rate_card_of_different_publishers_when_given_adslot_list()
    {
        $adslots = $this->getAdslots();

        $rate_card_priority = $this->createPriority();

        $company_1 = uniqid();
        $company_2 = uniqid();

        $this->createPriorityRateCards($this->rateCardTypeIds(), $this->getStartAndEndTime(), $rate_card_priority, $company_1);

        $this->createPriorityRateCards($this->rateCardTypeIds(), $this->getStartAndEndTime(), $rate_card_priority, $company_2);

        $get_specific_range_from_adslot_service_service = new GetSpecificRateCardFromAdslotLists($this->rateCardTypeIds(), '2019-03-25', '2019-03-28', $adslots);

        $get_specific_range_from_adslot_service = $get_specific_range_from_adslot_service_service->getRateFromAdslotListgroupedByPublishers();

        $this->assertEquals($company_1, $get_specific_range_from_adslot_service[0]['rate_card'][0][$company_1][0]->company_id);
        $this->assertEquals($company_2, $get_specific_range_from_adslot_service[0]['rate_card'][0][$company_2][0]->company_id);
    }

    public function test_it_always_returns_base_rate_card_when_specific_is_not_found()
    {
        //create_specific rate card
        $time = $this->getStartAndEndTime();
        $rate_card_type_id = $this->rateCardTypeIds();
        $rate_card_priority = $this->createPriority();
        $specific_rate_card = factory(Ratecard::class)->create([
            'ratecard_type' => $rate_card_priority['agency_priority'],
            'ratecard_type_id' => $rate_card_type_id['agency'],
            'duration' => 15,
            'start_time' => $time['start_time'],
            'end_time' => $time['end_time']
        ]);
        //create a base rate card
        $base_rate_card = factory(Ratecard::class)->create([
            'duration' => 30,
            'start_time' => $time['start_time'],
            'end_time' => $time['end_time']
        ]);

        $get_rate_card_service = new GetSpecificRateCard(30, '12:00:00', $rate_card_type_id, null, null);

        $this->assertEquals($base_rate_card->price, $get_rate_card_service->getRateCards()[0]->price);
    }

    public function getStartAndEndTime()
    {
        $start_time = date('Y-m-d H:i:s', strtotime('11:00'));
        $end_time = date('Y-m-d H:i:s', strtotime('15:00'));

        return ['start_time' => $start_time, 'end_time' => $end_time];
    }

    public function createPriority()
    {
        //create a rate card priority for agency
        $agency_priority = factory(RatecardPriority::class)->create();
        $brand_priority = factory(RatecardPriority::class)->create();
        $date_priority = factory(RatecardPriority::class)->create();
        return ['agency_priority' => $agency_priority['rate_card_type'], 'brand_priority' => $brand_priority['rate_card_type'],
                'date_priority' => $date_priority['rate_card_type']];
    }

    public function createPriorityRateCards($rate_card_ids, $start_end_time, $rate_card_priorities, $company_id)
    {
         $agency_rate = factory(Ratecard::class)->create([
                    'ratecard_type' => $rate_card_priorities['agency_priority'],
                    'ratecard_type_id' => $rate_card_ids['agency'],
                    'duration' => 15,
                    'start_time' => $start_end_time['start_time'],
                    'end_time' => $start_end_time['end_time'],
                    'company_id' => $company_id
                ]);

        $brand_rate = factory(Ratecard::class)->create([
            'ratecard_type' => $rate_card_priorities['brand_priority'],
            'ratecard_type_id' => $rate_card_ids['brand'],
            'duration' => 15,
            'start_time' => $start_end_time['start_time'],
            'end_time' => $start_end_time['end_time'],
            'company_id' => $company_id
        ]);

        $date_rate = factory(Ratecard::class)->create([
            'ratecard_type' => $rate_card_priorities['date_priority'],
            'start_date' => date('Y-m-d', strtotime('2019-03-24')),
            'end_date' => date('Y-m-d', strtotime('2019-03-29')),
            'duration' => 15,
            'start_time' => $start_end_time['start_time'],
            'end_time' => $start_end_time['end_time'],
            'company_id' => $company_id
        ]);

        return ['agency_rate' => $agency_rate, 'brand_rate' => $brand_rate, 'date_rate' => $date_rate];
    }

    public function rateCardTypeIds()
    {
        return [
                    'agency' => 'ztfgdgxgdjx',
                    'brand' => 'sfgdbxabhs'
                ];
    }

    public function createBaseRateCard($time, $duration, $company_id)
    {
        return $base_rate_card = \factory(Ratecard::class)->create([
                    'start_time' => $time['start_time'],
                    'end_time' => $time['end_time'],
                    'duration' => $duration,
                    'company_id' => $company_id
                ]);
    }

    public function createRateCardRange($time, $company_id)
    {
        //create factory
        \factory(Ratecard::class, 2)->create([
            'start_time' => $time['start_time'],
            'end_time' => $time['end_time'],
            'duration' => 15,
            'company_id' => $company_id
        ]);

        \factory(Ratecard::class, 2)->create([
            'start_time' => $time['start_time'],
            'end_time' => $time['end_time'],
            'duration' => 30,
            'company_id' => $company_id
        ]);
    }

    public function getAdslots()
    {
        return [
                    [
                        'file_duration' => 15,
                        'time_hour' => '12:00:00'
                    ],[
                        'file_duration' => 30,
                        'time_hour' => '13:00:00'
                    ]
                ];
    }

}
