<?php

namespace Tests\Feature\RateCard;

use Tests\TestCase;
use Vanguard\Libraries\Enum\RateCardTypes;
use Vanguard\Services\RateCard\StoreBaseRateCard;
use Vanguard\Services\RateCard\StoreSpecificRateCard;

class CreateRateCardServiceTest extends TestCase
{
    use \Tests\RateCardTrait\FakeRateCard;

   public function test_it_can_create_a_base_rate_card()
   {
       $faker = $this->getFakeRateCard();
       $store_base_rate_card = new StoreBaseRateCard($faker['company_id'], $faker['duration'], $faker['price'], $faker['start_time'],
           $faker['end_time'], RateCardTypes::BASE, $faker['title']);
       $base_rate_card = $store_base_rate_card->storeBaseRateCard();
       $this->assertEquals($base_rate_card->ratecard_type, RateCardTypes::BASE);
   }

   public function test_it_can_create_a_specific_rate_card_for_agency()
   {
       $faker = $this->getFakeRateCard();
       $store_base_rate_card = new StoreSpecificRateCard($faker['company_id'], $faker['duration'], $faker['price'], $faker['start_time'],
           $faker['end_time'], RateCardTypes::AGENCY, $faker['title'], null, null, $faker['rate_card_type_id']);
       $base_rate_card = $store_base_rate_card->storeBaseRateCard();
       $this->assertEquals($base_rate_card->ratecard_type, RateCardTypes::AGENCY);
   }

   public function test_it_can_create_a_specific_rate_card_for_brand()
   {
       $faker = $this->getFakeRateCard();
       $store_base_rate_card = new StoreSpecificRateCard($faker['company_id'], $faker['duration'], $faker['price'], $faker['start_time'],
           $faker['end_time'], RateCardTypes::BRAND, $faker['title'], null, null, $faker['rate_card_type_id']);
       $base_rate_card = $store_base_rate_card->storeBaseRateCard();
       $this->assertEquals($base_rate_card->ratecard_type, RateCardTypes::BRAND);
   }

   public function test_it_can_create_a_specific_rate_card_for_date()
   {
       $faker = $this->getFakeRateCard();
       $store_base_rate_card = new StoreSpecificRateCard($faker['company_id'], $faker['duration'], $faker['price'], $faker['start_time'],
           $faker['end_time'], RateCardTypes::DATE, $faker['title'], $faker['start_date'], $faker['end_date'], null);
       $base_rate_card = $store_base_rate_card->storeBaseRateCard();
       $this->assertEquals($base_rate_card->ratecard_type, RateCardTypes::DATE);
   }

}
