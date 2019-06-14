<?php

namespace Vanguard\Services\Schedule;

class PlaceAdForSchedule
{
    const TOTAL_SCHEDULABLE_DURATION_FOR_ADS = 720;
    protected $playout_date;
    protected $ad_pattern;
    protected $company_id;
    protected $media_program_id;
    protected $duration;
    protected $specified_hour;

    public function __construct($playout_date, $ad_pattern, $company_id, $media_program_id, $duration, $specified_hour)
    {
        $this->playout_date = $playout_date;
        $this->ad_pattern = $ad_pattern;
        $this->company_id = $company_id;
        $this->media_program_id = $media_program_id;
        $this->duration = $duration;
        $this->specified_hour = $specified_hour;
    }

    private function getHoursInTheProgram()
    {
        return \DB::table('time_belts')
                    ->selectRaw("hour(start_time) as playout_hours")
                    ->where('media_program_id', $this->media_program_id)
                    ->groupBy(\DB::raw('hour(start_time)'))
                    ->get()
                    ->toArray();
    }

    /*private function getAlreadyScheduledAds()
    {
        return \DB::table('time_belt_transactions')
                    ->selectRaw("playout_hour, sum(duration)")
                    ->where([
                        ['playout_date', $this->playout_date],
                        ['company_id', $this->company_id],
                    ])
                    ->when($this->specified_hour, function($query) {
                        return $query->whereRaw("hour(playout_hour) = '$this->specified_hour'");
                    })
                    ->whereIn(\DB::raw())
                    ->groupBy('playout_hour')
                    ->orderBy('playout_hour', 'desc')
                    ->get();
    }*/

    private function playoutHourIterator()
    {
        $playout_hours = $this->getHoursInTheProgram();
        $adbreak_mintes = 60 / $this->ad_pattern;
        $ad_breaks = [];
        foreach ($playout_hours as $playout_hour){
            for ($i = 0; $i < $this->ad_pattern; $i++){
                $added_minutes = $i * $minutes_in_adbreak;
                $ad_breaks[] = date('H:i:s', strtotime('+'.$added_minutes.' minutes', strtotime($playout_hour)));
            }
        }
        return $ad_breaks;
    }
}