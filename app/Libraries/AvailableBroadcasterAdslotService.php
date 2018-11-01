<?php

namespace Vanguard\Libraries;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

Class AvailableBroadcasterAdslotService
{
    public static function getActualDates($start_date, $end_date)
    {
        $dates = [];
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $dates[] =  $date->format('Y-m-d');
        }
        return $dates;
    }

    public static function groupCampaignDateByWeek($start_date, $end_date)
    {
        $actual_campaign_dates = AvailableBroadcasterAdslotService::getActualDates($start_date, $end_date);
        $byWeek = array();
        foreach ($actual_campaign_dates as $actual_campaign_date) {
            $date = \DateTime::createFromFormat('Y-m-d', $actual_campaign_date);
            $firstDayOfWeek = 1; //Monday
            $difference = ($firstDayOfWeek - $date->format('N'));
            if ($difference > 0) {
                $difference -= 7;
            }
            $date->modify("$difference days");
            $week = $date->format('W');
            if(!isset($byWeek[$week])){
                $byWeek[$week] = [];
            }
            $byWeek[$week][] = $actual_campaign_date;
        }
        return $byWeek;
    }

    public static function getWeekdaysFromGroupedCampaignDates($start_date, $end_date)
    {
        $actual_campaign_dates = AvailableBroadcasterAdslotService::getActualDates($start_date, $end_date);

        $byWeekDays = array();
        foreach ($actual_campaign_dates as $actual_campaign_date) {
            $date = \DateTime::createFromFormat('Y-m-d', $actual_campaign_date);

            $firstDayOfWeek = 1; //Monday

            $difference = ($firstDayOfWeek - $date->format('N'));
            if ($difference > 0) {
                $difference -= 7;
            }
            $date->modify("$difference days");
            $week = $date->format('W');

            if(!isset($byWeekDays[$week])){
                $byWeekDays[$week] = [];
            }

            $byWeekDays[$week][date('l', strtotime($actual_campaign_date))] = $actual_campaign_date;
        }

        return $byWeekDays;
    }
}