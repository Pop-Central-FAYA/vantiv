<?php

namespace Vanguard\Services\Adslot;

use Carbon\CarbonPeriod;
use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\Broadcaster\BroadcasterDetails;
use Vanguard\Services\Company\CompanyDetails;
use Vanguard\Services\Day\DayDetails;

class AdslotFilterResult
{
    protected $campaign_general_information;
    protected $start_date;
    protected $end_date;

    public function __construct($campaign_general_information, $start_date, $end_date)
    {
        $this->campaign_general_information = $campaign_general_information;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function run()
    {
        return $this->adslotFilterResult();
    }

    public function adslotFilterResult()
    {
        $ratecards = $this->getRatecardsBetweenCampaignDates();
        $adslot_filter = Utilities::switch_db('api')->table('adslots')
                                    ->join('companies', 'companies.id', '=', 'adslots.company_id')
                                    ->select('adslots.company_id AS broadcaster',
                                                'companies.logo AS broadcaster_logo',
                                                'companies.name AS broadcaster_brand')
                                    ->selectRaw("COUNT(adslots.id) as total_slot")
                                    ->where([
                                        ['adslots.min_age','>=', $this->campaign_general_information->min_age],
                                        ['adslots.max_age','<=', $this->campaign_general_information->max_age],
                                        ['adslots.is_available', 0]
                                    ])
                                    ->whereIn('adslots.target_audience', $this->campaign_general_information->target_audience)
                                    ->whereIn('adslots.day_parts', $this->campaign_general_information->dayparts)
                                    ->whereIn('adslots.region', $this->campaign_general_information->region)
                                    ->whereIn('adslots.rate_card', $ratecards)
                                    ->when(\Auth::user()->company_type == CompanyTypeName::BROADCASTER, function($query) {
                                        return $query->where([
                                                    ['adslots.company_id', \Auth::user()->companies->first()->id],
                                                    ['adslots.channels', \Auth::user()->companies->first()->channels->first()->id]
                                                ]);
                                    })
                                    ->when(\Auth::user()->company_type == CompanyTypeName::AGENCY, function ($query) {
                                        return $query->whereIn('adslots.channels', $this->campaign_general_information->channel)
                                                     ->groupBy('adslots.company_id');
                                    })
                                    ->get();
        return $adslot_filter;
    }

    public function getRatecardsBetweenCampaignDates()
    {
        $day_id = [];
        $ratecards_array = [];
        $campaign_dates = $this->getDatesInbetweenCampaign();
        foreach ($campaign_dates as $campaign_date){
            $day_name = date('l', strtotime($campaign_date));
            $day_object = new DayDetails(null, $day_name);
            $day_object = $day_object->getDayDetails();
            $day_id[] = $day_object->id;
        }

        $ratecards = Utilities::switch_db('api')->table('rateCards')
                                ->select('id')
                                ->whereIn('day', $day_id)
                                ->get()
                                ->toArray();

        foreach ($ratecards as $ratecard){
            $ratecards_array[] = $ratecard->id;
        }

        return $ratecards_array;
    }

    public function getDatesInbetweenCampaign()
    {
        $campaign_dates = [];

        $date_period = CarbonPeriod::create($this->start_date, $this->end_date);

        foreach ($date_period as $date){
            $campaign_dates[] = $date->format('Y-m-d');
        }

        return $campaign_dates;

    }

}
