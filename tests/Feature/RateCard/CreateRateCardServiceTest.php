<?php

namespace Tests\Feature\RateCard;

use Tests\TestCase;
use Vanguard\Libraries\Enum\RateCardTypes;
use Vanguard\Services\RateCard\StoreBaseRateCard;
use Vanguard\Services\RateCard\StoreSpecificRateCard;

class CreateRateCardServiceTest extends TestCase
{
    use \Tests\RateCardTrait\FakeRateCard;

   public function test_it_can_create_rate_card()
   {
       $faker = $this->getFakeRateCard();
       $store_base_rate_card = new StoreBaseRateCard($faker['company_id'], $this->durationList(), $this->priceList(), $faker['title']);
       $store_base_rate_card->storeRateCardWithDurationAndPrice();
       $this->assertDatabaseHas('rate_cards', [
           'company_id' => $faker['company_id']
       ]);
   }

   private function durationList()
   {
       return [
           6000, 7000, 8000, 10000
       ];
   }

   private function priceList()
   {
       return [
           15, 30, 45, 60
       ];
   }

}
