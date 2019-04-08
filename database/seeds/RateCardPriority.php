<?php

use Illuminate\Database\Seeder;
use Vanguard\Models\RatecardPriority as Priority;
use Vanguard\Libraries\Enum\RateCardTypes;

class RateCardPriority extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rate_card_priority1 = [
            'rate_card_type' => RateCardTypes::DATE,
            'priority' => 1
        ];

        $rate_card_priority2 = [
            'rate_card_type' => RateCardTypes::AGENCY,
            'priority' => 2
        ];

        $rate_card_priority3 = [
            'rate_card_type' => RateCardTypes::BRAND,
            'priority' => 3
        ];

        $rate_card_priority4 = [
            'rate_card_type' => RateCardTypes::BASE,
            'priority' => 4
        ];

        Priority::create($rate_card_priority1);
        Priority::create($rate_card_priority2);
        Priority::create($rate_card_priority3);
        Priority::create($rate_card_priority4);
    }
}
