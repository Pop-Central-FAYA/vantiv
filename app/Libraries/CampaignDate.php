<?php

namespace Vanguard\Libraries;

use Carbon\CarbonPeriod;

Class CampaignDate
{
    public function getActualDates($start_date, $end_date)
    {
        $dates = [];
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $dates[] =  $date;
        }
        return $dates;
    }

    //we might wanna definitely switch to using carbon library
    public function groupCampaignDateByWeek($start_date, $end_date)
    {
        $actual_campaign_dates = $this->getActualDates($start_date, $end_date);
        $byWeek = array();
        foreach ($actual_campaign_dates as $actual_campaign_date) {
            $week = $actual_campaign_date->format('W');
            if(!isset($byWeek[$week])){
                $byWeek[$week] = [];
            }
            $byWeek[$week][$actual_campaign_date->format('l')] = $actual_campaign_date->format('Y-m-d');
        }
        return $byWeek;
    }

    public function getFirstWeek($start_date, $end_date)
    {
        $campaignWeeks = $this->groupCampaignDateByWeek($start_date, $end_date);
        $first_week = reset($campaignWeeks);
        return $first_week;
    }

    public function getStartAndEndDateForFirstWeek($first_week)
    {
        $start_date_of_the_week = reset($first_week);
        $end_date_of_the_week = end($first_week);
        return (['start_date_of_the_week' => $start_date_of_the_week, 'end_date_of_the_week' => $end_date_of_the_week]);
    }

}
