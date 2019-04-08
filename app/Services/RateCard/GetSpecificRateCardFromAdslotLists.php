<?php

namespace Vanguard\Services\RateCard;

class GetSpecificRateCardFromAdslotLists
{
    protected $is_specific_id_list; //this is a list of the agency and brand id which could have a specific rate cards
    protected $start_date;
    protected $end_date;
    protected $adslots;

    public function __construct($is_specific_id_list, $start_date, $end_date, $adslots)
    {
        $this->is_specific_id_list = $is_specific_id_list;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->adslots = $adslots;
    }

    public function getRateFromAdslotList()
    {
        $specific_rates = [];
        foreach ($this->adslots as $adslot){
            $specific_rate_service = new GetSpecificRateCard($adslot['file_duration'], $adslot['time_hour'], $this->is_specific_id_list,
                $this->start_date, $this->end_date);
            $specific_rates[] = [
                'file_duration' => $adslot['file_duration'],
                'time_hour' => $adslot['time_hour'],
                'rate_card' => [$specific_rate_service->getRateCards()]
            ];
        }
        return $specific_rates;
    }

    public function getRateFromAdslotListgroupedByPublishers()
    {
        $specific_rates = [];
        foreach ($this->adslots as $adslot){
            $specific_rate_service = new GetSpecificRateCard($adslot['file_duration'], $adslot['time_hour'], $this->is_specific_id_list,
                $this->start_date, $this->end_date);
            $specific_rates[] = [
                'file_duration' => $adslot['file_duration'],
                'time_hour' => $adslot['time_hour'],
                'rate_card' => [$specific_rate_service->groupRateCardByPublishers()]
            ];
        }
        return $specific_rates;
    }
}
