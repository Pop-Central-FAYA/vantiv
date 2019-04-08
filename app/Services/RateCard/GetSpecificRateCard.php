<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Libraries\Enum\RateCardTypes;

class GetSpecificRateCard
{
    protected $file_duration;
    protected $time_hour;
    protected $is_specific_id_list; //this is a list of the agency and brand id which could have a specific rate cards
    protected $start_date;
    protected $end_date;


    public function __construct($file_duration, $time_hour, $is_specific_id_list, $start_date, $end_date)
    {
        $this->file_duration = $file_duration;
        $this->time_hour = $time_hour;
        $this->is_specific_id_list = $is_specific_id_list;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function getRateCards()
    {
        return \DB::table('rate_cards')
                    ->select('rate_cards.price', 'rate_cards.ratecard_type', 'rate_cards.duration', 'ratecard_priorities.priority', 'rate_cards.company_id')
                    ->leftJoin('ratecard_priorities', 'ratecard_priorities.rate_card_type', '=', 'rate_cards.ratecard_type')
                    ->where('rate_cards.duration', $this->file_duration)
                    ->Where(function ($query){
                        $query->whereTime('rate_cards.start_time', '<=', $this->time_hour);
                        $query->whereTime('rate_cards.end_time', '>', $this->time_hour);
                    })
                    ->when($this->is_specific_id_list['agency'], function ($query) {
                        return $query->orWhere([
                                    ['rate_cards.ratecard_type', RateCardTypes::AGENCY],
                                    ['rate_cards.ratecard_type_id', $this->is_specific_id_list['agency']]
                                ]);
                    })
                    ->when($this->is_specific_id_list['brand'], function ($query) {
                        return $query->orWhere([
                                    ['rate_cards.ratecard_type', RateCardTypes::BRAND],
                                    ['rate_cards.ratecard_type_id', $this->is_specific_id_list['brand']]
                                ]);
                    })
                    ->when($this->start_date && $this->end_date, function ($query){
                        return $query->orWhere([
                                    ['rate_cards.ratecard_type', RateCardTypes::DATE],
                                    ['rate_cards.start_date', '<=', $this->start_date],
                                    ['rate_cards.end_date', '>=', $this->end_date]
                                ]);
                    })
                    ->orderBy('ratecard_priorities.priority', 'asc')
                    ->get();
    }

    public function groupRateCardByPublishers()
    {
        return $this->getRateCards()->groupBy('company_id');
    }
}
