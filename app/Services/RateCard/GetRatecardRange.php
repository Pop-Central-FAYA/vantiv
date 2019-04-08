<?php

namespace Vanguard\Services\RateCard;

class GetRatecardRange
{
    protected $adslots;

    public function __construct($adslots)
    {
        $this->adslots = $adslots;
    }

    public function getRatecardRange()
    {
        $ratecards = [];
        foreach ($this->adslots as $adslot){
            $single_ratecard_service = new GetRateCards($adslot['file_duration'], $adslot['time_hour']);
            $ratecards[] = [
                'file_duration' =>  $adslot['file_duration'],
                'time_hour' => $adslot['time_hour'],
                'rate_card' => [$single_ratecard_service->getRateCards()]
            ];
        }
        return $ratecards;
    }

    public function getRangeGroupByPublishers()
    {
        $ratecards = [];
        foreach ($this->adslots as $adslot){
            $single_ratecard_service = new GetRateCards($adslot['file_duration'], $adslot['time_hour']);
            $ratecards[] = [
                'file_duration' =>  $adslot['file_duration'],
                'time_hour' => $adslot['time_hour'],
                'rate_card' => [$single_ratecard_service->groupRateCardsByPublishers()]
            ];
        }
        return $ratecards;
    }
}
