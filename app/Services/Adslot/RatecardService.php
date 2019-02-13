<?php

namespace Vanguard\Services\Adslot;

use Vanguard\Libraries\CampaignDate;
use Vanguard\Libraries\Utilities;

class RatecardService
{
    protected $campaign_general_information;
    protected $start_date;
    protected $end_date;
    protected $channel_id;
    protected $company_id;

    public function __construct($campaign_general_information, $start_date, $end_date, $channel_id, $company_id)
    {
        $this->campaign_general_information = $campaign_general_information;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->channel_id = $channel_id;
        $this->company_id = $company_id;
    }

    public function run()
    {
        $new_rate_cards = [];
        $rate_cards = $this->getRateCards();
        foreach ($rate_cards['rate_cards'] as $rate_card){
            $adslots = $this->filterAdslots(json_decode(json_encode($rate_cards['adslots_from_campaign_filter']), true), $rate_card->day_id);
            $new_rate_cards[] = [
                'id' => $rate_card->id,
                'day' => $rate_card->day,
                'day_id' => $rate_card->day_id,
                'adslot' => $adslots,
                'all_adslots' => $rate_cards['adslot_differences'],
                'start_date' => $rate_cards['campaign_dates_in_first_week']['start_date_of_the_week'],
                'end_date' => $rate_cards['campaign_dates_in_first_week']['end_date_of_the_week'],
                'actual_date' => $rate_cards['days_in_first_week'][$rate_card->day]
            ];
        }

        return ['rate_cards' => $new_rate_cards, 'adslots' => $rate_cards['adslots_from_campaign_filter']];

    }

    public function getRateCards()
    {
        $campaign_date_object = new CampaignDate();
        $days_in_first_week = $campaign_date_object->getFirstWeek($this->start_date, $this->end_date);
        $campaign_dates_in_first_week = $campaign_date_object->getStartAndEndDateForFirstWeek($days_in_first_week);
        $adslot_filter_object = new AdslotFilterResult(null,
                                                        $campaign_dates_in_first_week['start_date_of_the_week'],
                                                        $campaign_dates_in_first_week['end_date_of_the_week']);
        $rate_card_ids = $adslot_filter_object->getRatecardsBetweenCampaignDates();
        $all_broadcaster_adslots = $this->getAllBroadcasterAdslots();
        $adslots_from_campaign_filter = $this->getAdslotsFromCampaignResult();
        $adslots_differences = $this->getAdslotDifference($all_broadcaster_adslots, $adslots_from_campaign_filter);
        return [
                'rate_cards' => $this->getRateCardsGroupByDay($rate_card_ids),
                'adslots_from_campaign_filter' => $adslots_from_campaign_filter,
                'all_broadcaster_adslots' => $all_broadcaster_adslots,
                'adslot_differences' => $adslots_differences,
                'days_in_first_week' => $days_in_first_week,
                'campaign_dates_in_first_week' => $campaign_dates_in_first_week
            ];
    }

    public function getAllBroadcasterAdslots()
    {
        return Utilities::switch_db('api')->table('adslots')
                                    ->join('rateCards', 'rateCards.id', '=', 'adslots.rate_card')
                                    ->select('adslots.id AS adslot_id','adslots.from_to_time',
                                                    'rateCards.day AS day_id'
                                    )
                                    ->where('adslots.company_id', $this->company_id)
                                    ->get();
    }

    public function getAdslotsFromCampaignResult()
    {
        return Utilities::switch_db('api')->table('adslots')
                                    ->join('adslotPrices', 'adslotPrices.adslot_id', '=', 'adslots.id')
                                    ->join('rateCards', 'rateCards.id', '=', 'adslots.rate_card')
                                    ->leftJoin('adslotPercentages', 'adslotPercentages.adslot_id', '=', 'adslots.id')
                                    ->selectRaw("adslots.*, 
                                                            rateCards.day AS day_id,
                                                            IF(adslots.id = adslotPercentages.adslot_id,adslotPercentages.price_60,adslotPrices.price_60) AS price_60,
                                                            IF(adslots.id = adslotPercentages.adslot_id,adslotPercentages.price_45,adslotPrices.price_45) AS price_45,
                                                            IF(adslots.id = adslotPercentages.adslot_id,adslotPercentages.price_30,adslotPrices.price_30) AS price_30,
                                                            IF(adslots.id = adslotPercentages.adslot_id,adslotPercentages.price_15,adslotPrices.price_15) AS price_15
                                    ")
                                    ->whereIn('adslots.target_audience', $this->campaign_general_information->target_audience)
                                    ->whereIn('adslots.day_parts', $this->campaign_general_information->dayparts)
                                    ->whereIn('adslots.region', $this->campaign_general_information->region)
                                    ->where([
                                        ['adslots.min_age', '>=', $this->campaign_general_information->min_age],
                                        ['adslots.max_age', '<=', $this->campaign_general_information->max_age],
                                        ['adslots.is_available', 0],
                                        ['adslots.channels', $this->channel_id],
                                        ['adslots.company_id', $this->company_id]
                                    ])
                                    ->get();
    }

    public function getAdslotDifference($all_broadcasters_adslots, $all_adslots_from_campaign_filter)
    {
        $all_broadcasters_adslots_array = [];
        $all_adslots_from_campaign_filter_array = [];
        $adslot_difference_array = [];
        foreach ($all_broadcasters_adslots as $all_broadcaster_adslot){
            $all_broadcasters_adslots_array[$all_broadcaster_adslot->adslot_id][] = $all_broadcaster_adslot;
        }

        foreach ($all_adslots_from_campaign_filter as $all_adslot_from_campaign){
            $all_adslots_from_campaign_filter_array[$all_adslot_from_campaign->id][] = $all_adslot_from_campaign;
        }

        foreach ($all_broadcasters_adslots_array as $key => $value){
            if(array_key_exists($key, $all_adslots_from_campaign_filter_array)){
                $adslot_difference_array[] = $all_adslots_from_campaign_filter_array[$key];
            }else{
                $adslot_difference_array[] = $value;
            }

        }

        return $adslot_difference_array;
    }

    public function getAdslotByDayId($all_broadcasters_adslots, $day_id)
    {
        $matched_adslots = [];
        foreach($all_broadcasters_adslots as $all_broadcasters_adslot){
            if($all_broadcasters_adslot['day_id'] === $day_id)
                $matched_adslots[] = (object)$all_broadcasters_adslot;
        }
        return $matched_adslots;
    }

    public function getRateCardsGroupByDay($ratecards)
    {
        return Utilities::switch_db('api')->table('rateCards')
                                    ->join('days', 'days.id', '=', 'rateCards.day')
                                    ->whereIn('rateCards.id', function ($query) use ($ratecards) {
                                        $query->select('rate_card')
                                            ->from('adslots')
                                            ->where([
                                                ['adslots.min_age','>=', $this->campaign_general_information->min_age],
                                                ['adslots.max_age','<=', $this->campaign_general_information->max_age],
                                                ['adslots.company_id', $this->company_id]
                                            ])
                                            ->whereIn('adslots.target_audience', $this->campaign_general_information->target_audience)
                                            ->whereIn('adslots.day_parts', $this->campaign_general_information->dayparts)
                                            ->whereIn('adslots.region', $this->campaign_general_information->region)
                                            ->whereIn('adslots.rate_card', $ratecards);
                                    })
                                    ->select('days.day', 'rateCards.day AS day_id', 'rateCards.id')
                                    ->groupBy('rateCards.day')
                                    ->get();
    }

    public function filterAdslots($adslots, $day)
    {
        $matches = array();
        foreach($adslots as $adslot){
            if($adslot['day_id'] === $day)
                $matches[] = (object)$adslot;
        }
        return $matches;
    }
}
