<?php

namespace Vanguard\Services\Adslot;

class Adslotlist
{
    protected $company_id;
    protected $day;

    public function __construct($company_id, $day)
    {
        $this->company_id = $company_id;
        $this->day = $day;
    }

    public function adslotsListQuery()
    {
        return \DB::table('adslots')
                    ->join('adslotPrices', 'adslotPrices.adslot_id', '=', 'adslots.id')
                    ->leftJoin('adslotPercentages', 'adslotPercentages.adslot_id', '=', 'adslots.id')
                    ->join('rateCards', 'rateCards.id', '=', 'adslots.rate_card')
                    ->join('days', 'days.id', '=', 'rateCards.day')
                    ->selectRaw("adslots.id, adslots.from_to_time, days.day, adslotPercentages.percentage,
                        IF(adslots.id = adslotPercentages.adslot_id, adslotPercentages.price_60, adslotPrices.price_60) as price_60,
                        IF(adslots.id = adslotPercentages.adslot_id, adslotPercentages.price_45, adslotPrices.price_45) as price_45,
                        IF(adslots.id = adslotPercentages.adslot_id, adslotPercentages.price_30, adslotPrices.price_30) as price_30,
                        IF(adslots.id = adslotPercentages.adslot_id, adslotPercentages.price_15, adslotPrices.price_15) as price_15")
                    ->where('adslots.company_id', $this->company_id)
                    ->when($this->day, function ($query) {
                        return $query->where('days.day', 'like', '%'.$this->day.'%');
                    })
                    ->get();
    }

    public function adlotsLists()
    {
        $adslots_lists = [];
        foreach ($this->adslotsListQuery() as $adslot){
            $adslots_lists[] = [
                'id' => $adslot->id,
                'day' => $adslot->day,
                'time_slot' => $adslot->from_to_time,
                '60_seconds' => $adslot->price_60,
                '45_seconds' => $adslot->price_45,
                '30_seconds' => $adslot->price_30,
                '15_seconds' => $adslot->price_15,
                'percentage' => $adslot->percentage
            ];
        }

        return $adslots_lists;
    }
}
