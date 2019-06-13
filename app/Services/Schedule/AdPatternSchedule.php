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
        $get_scheduled_ads = $this->getScheduledAds();
        if($this->getAvailableSpace($get_scheduled_ads) > $this->time_belt->duration){
            $total_scheduled_durations = $get_scheduled_ads->sum('duration');
            $total_new_durations = $total_scheduled_durations + $this->time_belt->duration;
            $this->processScheduleAds($get_scheduled_ads, $total_new_durations);
            return 'success';
        }else{
            return 'error';
        }
    }

    private function processScheduleAds($get_scheduled_ads, $total_new_durations)
    {
        foreach ($this->splitHourToAdBreak() as $adbreak){
            if($adbreak['start_duration'] < $total_new_durations && $adbreak['end_duration'] > $total_new_durations){
                $last_scheduled_ad = $this->getScheduledAdByAdBreak($get_scheduled_ads, $adbreak['start_time'])->last();
                $order = $last_scheduled_ad ? $last_scheduled_ad->order + 1 : 1;
                $this->scheduleAds($order, $adbreak['start_time'], $this->time_belt->broadcaster_id);
                break;
            }
        }
    }

    /**
     * @return int
     * this method get the available ad space in the hour for a publisher in a particular date
     * e.g if the playout hour is 11:00:00, the method returns how many seconds is left
     * for ads to be scheduled
     */
    private function getAvailableSpace($get_scheduled_ads)
    {
        $scheduled_duration = $get_scheduled_ads->sum('duration');
        return self::TOTAL_SCHEDULABLE_DURATION - $scheduled_duration;
    }

    /**
     * @return mixed
     * This method returns all the scheduled ads in the hour for a publisher on a specified date
     */
    private function getScheduledAds()
    {
        $scheduled_ads = new GetAdsPerHour($this->time_belt->playout_date, $this->time_belt->playout_hour,
                                            $this->time_belt->broadcaster_id);
        return $scheduled_ads->run();
    }

    /**
     * @return array
     * This method returns a breakdown of how the adbreaks in an hour will look like, it returns the following structure
     * [
     *      [
     *          'start_time' => '11:00:00',
     *          'start_duration' => 0,
     *          'end_duration' => 180
     *      ],
     *      [
     *          'start_time' => '11:15:00',
     *          'start_duration' => '180',
     *          'end_duration' => '360'
     *      ]
     * ]
     */
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

    /**
     * @param $ad_break
     * @return mixed
     * Returns the scheduled ad based on the supplied ad_break
     */
    private function getScheduledAdByAdBreak($get_scheduled_ads,$ad_break)
    {
        return $get_scheduled_ads->where('ad_break', $ad_break);
    }

    /**
     * @param $order
     * @param $ad_break
     * @param $company_id
     * @return string
     * Update the time belt transaction table with the schedule information
     */
    private function scheduleAds($order, $ad_break, $company_id)
    {
        $schedule = Schedule::where('id', $this->time_belt_transaction_id)->first();
        $schedule->order = $order;
        $schedule->ad_break = $ad_break;
        $schedule->company_id = $company_id;
        $schedule->save();
        return 'success';
    }
}