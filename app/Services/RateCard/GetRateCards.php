<?php

namespace Vanguard\Services\RateCard;

class GetRateCards
{
    protected $file_duration;
    protected $time_hour;

    public function __construct($file_duration, $time_hour)
    {
        $this->file_duration = $file_duration;
        $this->time_hour = $time_hour;
    }

    public function getRateCards()
    {
        return \DB::table('rate_cards')
                    ->select('price', 'start_time', 'end_time', 'company_id')
                    ->where('duration', $this->file_duration)
                    ->where(function ($query) {
                        $query->whereTime('start_time', '<=', $this->time_hour);
                        $query->whereTime('end_time', '>', $this->time_hour);
                    })
                    ->get();
    }

    public function groupRateCardsByPublishers()
    {
        return $this->getRateCards()->groupBy('company_id');
    }
}
