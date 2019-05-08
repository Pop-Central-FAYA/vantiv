<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Libraries\Enum\ProgramStatus;
use Vanguard\Models\MediaProgram;
use Vanguard\Models\Ratecard\Ratecard;

class GetRateCardForStation
{
    protected $station_id;

    public function __construct($station_id)
    {
        $this->station_id = $station_id;
    }

    public function getRateCardDurations()
    {
        return Ratecard::with('rate_card_durations', 'company')
                        ->where('status', ProgramStatus::ACTIVE)
                        ->whereIn('company_id', $this->station_id)
                        ->get();
    }

    public function formatRateCardData()
    {
        $rate_cards = [];
        foreach ($this->getRateCardDurations() as $rate_card){
            $price_15 = 0;
            $price_30 = 0;
            $price_45 = 0;
            $price_60 = 0;
            foreach ($rate_card->rate_card_durations as $rate_card_duration){
                if($rate_card_duration->duration == 15){
                    $price_15 = $rate_card_duration->price;
                }elseif ($rate_card_duration->duration == 30){
                    $price_30 = $rate_card_duration->price;
                }elseif ($rate_card_duration->duration == 45){
                    $price_45 = $rate_card_duration->price;
                }else{
                    $price_60 = $rate_card_duration->price;
                }
            }
            $rate_cards[] = [
                'id' => $rate_card->id,
                'title' => $rate_card->title,
                'revenue' => 0,
                'price_15' => number_format($price_15,2),
                'price_30' => number_format($price_30,2),
                'price_45' => number_format($price_45, 2),
                'price_60' => number_format($price_60,2),
                'station' => $rate_card->company->name
            ];
        }
        return $rate_cards;
    }
}
