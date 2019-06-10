<?php

namespace Vanguard\Services\Schedule;

use Vanguard\Models\Schedule;

class AdPatternSchedule
{
    protected $time_belt;
    protected $ad_pattern;
    const TOTAL_SCHEDULABLE_DURATION = 720;
    protected $time_belt_transaction_id;

    public function __construct($time_belt, $ad_pattern, $time_belt_transaction_id)
    {
        $this->time_belt = $time_belt;
        $this->ad_pattern = $ad_pattern;
        $this->time_belt_transaction_id = $time_belt_transaction_id;
    }

    public function run()
    {
        if($this->check_duration_fits_hour() > $this->time_belt->duration){
            $total_scheduled_durations = $this->getScheduledAds()->sum('duration');
            $total_new_durations = $total_scheduled_durations + $this->time_belt->duration;
            foreach ($this->splitHourToAdBreak() as $adbrak){
                if($adbrak['start_duration'] < $total_new_durations && $adbrak['end_duration'] > $total_new_durations){
                    $last_scheduled_ad = $this->getScheduledAdByAdBreak($adbrak['start_time'])->last();
<<<<<<< HEAD
                    $order = $last_scheduled_ad ? $last_scheduled_ad->order + 1 : 1;
=======
                    $order = $last_scheduled_ad->order + 1;
>>>>>>> e40e2931c980cbdf82f6687982cdc76923826123
                    $this->scheduleAds($order, $adbrak['start_time'], $this->time_belt->broadcaster_id);
                    break;
                }
            }
            return 'success';
        }else{
            return 'error';
        }
    }

    private function check_duration_fits_hour()
    {
        $scheduled_duration = $this->getScheduledAds()->sum('duration');
        return self::TOTAL_SCHEDULABLE_DURATION - $scheduled_duration;
    }

    private function getScheduledAds()
    {
        $scheduled_ads = new GetAdsPerHour($this->time_belt->playout_date, $this->time_belt->playout_hour,
                                            $this->time_belt->broadcaster_id);
        return $scheduled_ads->run();
    }

    private function splitHourToAdBreak()
    {
        $minutes_in_adbreak = 60 / $this->ad_pattern;
        $ad_break_array = [];
        for ($i = 0; $i < $this->ad_pattern; $i++){
            $added_minutes = $i * $minutes_in_adbreak;
            $ad_break_array[] = [
                'start_time' => date('H:i:s', strtotime('+'.$added_minutes.' minutes', strtotime($this->time_belt->playout_hour))),
                'start_duration' => $i * $minutes_in_adbreak * 60,
                'end_duration' => ($i+1) * $minutes_in_adbreak * 60
            ];
        }
        return $ad_break_array;
    }

    private function getScheduledAdByAdBreak($ad_break)
    {
<<<<<<< HEAD
        return $this->getScheduledAds()->where('ad_break', $ad_break);
=======
        return $this->getScheduledAds()->where('ad_break', $ad_break)->get();
>>>>>>> e40e2931c980cbdf82f6687982cdc76923826123
    }

    private function scheduleAds($order, $ad_break, $company_id)
    {
<<<<<<< HEAD
        $schedule = Schedule::where('id', $this->time_belt_transaction_id)->first();
=======
        $schedule = Schedule::where('id', $this->time_belt_transaction_id);
>>>>>>> e40e2931c980cbdf82f6687982cdc76923826123
        $schedule->order = $order + 1;
        $schedule->ad_break = $ad_break;
        $schedule->company_id = $company_id;
        $schedule->save();
        return 'success';
    }
}