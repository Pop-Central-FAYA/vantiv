<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Libraries\DayPartList;
use Vanguard\Services\Traits\GetDayPartTrait;


class ExportCampaignMpo
{
    protected $campaign_mpo_time_belts;

    use GetDayPartTrait;

    public function __construct($campaign_mpo_time_belts)
    {
        $this->campaign_mpo_time_belts = $campaign_mpo_time_belts;
    }

    public function run()
    {
        $mpos = [];
        foreach($this->groupByDayPart() as $station => $station_time_belts){
            foreach($station_time_belts as $program => $time_belts){
                foreach($time_belts as $duration => $slots){
                    foreach($slots as $day_part => $ads){
                        foreach($ads as $month => $ad){
                            $mpos[] = [
                                'duration' => $duration,
                                'station' => $station,
                                'program' => $program,
                                'daypart' => $day_part,
                                'time_slot' => DayPartList::DAYPARTS[$day_part],
                                'day_range' => $this->daysRange($ad),
                                'month' => date('M y', strtotime($month)),
                                'slots' => $ad,
                                'exposures' => $this->pluckExposure($ad),
                                'total_slot' => $this->getTotalSlot($ad)
                            ];
                        }
                    }
                }
            }
        }
        return $mpos;
    }

    public function groupByDayPart()
    {
        return $this->campaign_mpo_time_belts->map(function($station_item) {
            return $station_item->map(function($item) {
                return $item->map(function($ads) {
                    return $ads->map(function($ad) {
                        return collect($ad)->put('day_part', $this->getDayPart($ad['time_belt_start_time'])['name']);
                    })->groupBy(['day_part', 'month', 'playout_date']);  
                });
            });
        });
    }

    private function pluckExposure($ads)
    {
        return $ads->mapWithKeys(function ($item) {
            return [(int)$item[0]['day_number'] => $item->sum('ad_slots')];
        });
    }

    private function daysRange($ads)
    {
        $day_name = [];
        foreach($ads as $date => $ad){
            $day_name[] = date('D', strtotime($date));
        }
        if(current($day_name) != end($day_name)){
            return current($day_name) .' - '.end($day_name);
        }else{
            return current($day_name);
        }
    }

    public function getTotalSlot($ads)
    {
        $slot = 0;
        foreach($ads as $ad){
            $slot += $ad->sum('ad_slots');
        }
        return $slot;
    }
}