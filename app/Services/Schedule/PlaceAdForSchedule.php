<?php

namespace Vanguard\Services\Schedule;

use Http\Client\Exception\HttpException;
use Matrix\Exception;
use Vanguard\Models\Schedule;
use Vanguard\Models\TimeBeltTransaction;

class PlaceAdForSchedule
{
    const TOTAL_SCHEDULABLE_DURATION_FOR_AN_HOUR = 720;
    protected $ad_pattern;
    protected $time_belt_transaction_id;
    protected $time_belt;
    protected $preferred_hours; //This is an array like [10, 11, 12]
    protected $start_time; //A time if format of hh:mm:ss

    public function __construct($ad_pattern, $time_belt_transaction_id, $time_belt, $preferred_hours, $start_time)
    {
        $this->ad_pattern = $ad_pattern;
        $this->time_belt_transaction_id = $time_belt_transaction_id;
        $this->time_belt = $time_belt;
        $this->preferred_hours = $preferred_hours;
        $this->start_time = $start_time;
    }

    public function run()
    {
        $per_break_duration = self::TOTAL_SCHEDULABLE_DURATION_FOR_AN_HOUR / $this->ad_pattern;
        $scheduled_list = $this->buildScheduledList();
        foreach ($scheduled_list as $schedule){
            if($per_break_duration >= ($schedule['duration'] + $this->time_belt->duration)){
                $order = $schedule['total_order'] + 1;
                return $this->updateTimeBeltTransactions($order, $schedule['ad_break'], $this->time_belt_transaction_id);
            }
        }
        return null;
    }

    private function updateTimeBeltTransactions($order, $playout_hour, $time_belt_transaction_id)
    {
        $schedule = Schedule::find($time_belt_transaction_id);
        $schedule->order = $order;
        $schedule->playout_hour = $playout_hour;
        $schedule->save();
        return $schedule;
    }

    private function buildScheduledList()
    {
        $scheduled_list = [];
        $playout_iterator = $this->adBreakList();
        $get_schedule = \DB::table('time_belt_transactions')
                        ->selectRaw("SUM(duration) as total_duration, SUM(`order`) as number_of_scheduled, playout_hour")
                        ->where([
                            ['playout_date', $this->time_belt->playout_date],
                            ['company_id', $this->time_belt->company_id]
                        ])
                        ->groupBy('playout_hour')
                        ->orderBy('playout_hour', 'desc')
                        ->get();
        foreach ($playout_iterator as $key => $ad_break){
            $schedule = $get_schedule->where('playout_hour', $ad_break)->first();
            $duration = 0;
            $number_of_scheduled = 0;
            if($schedule){
                $duration = $schedule->total_duration;
                $number_of_scheduled = $schedule->number_of_scheduled;
            }
            $scheduled_list[] = [
                'ad_break' => $ad_break,
                'duration' => (int)$duration,
                'total_order' => $number_of_scheduled
            ];
        }
        return $scheduled_list;
    }

    /**
     * @return array
     * given 10 and 11
     * if ad_pattern is 4
     * [
     *  10:00
     *  10:15
     *  10:30
     *  10:45
     *  11:00
     *  11:15
     *  11:30
     *  11:45
     * ]
     * if ad_pattern is 3
     * [
     *  10:00
     *  10:20
     *  10:40
     *  11:00
     *  11:20
     *  11:40
     * ]
     */
    private function adBreakList()
    {
        $start_time = $this->start_time ? $this->start_time : '00:00:00';
        $adbreak_minutes = 60 / $this->ad_pattern;
        $ad_breaks = [];
        foreach ($this->preferred_hours as $preferred_hour){
            $formatted_preferred_hour = $preferred_hour.':00:00';
            for ($i = 0; $i < $this->ad_pattern; $i++){
                $added_minutes = $i * $adbreak_minutes;
                $new_break = strtotime('+'.$added_minutes.' minutes', strtotime($formatted_preferred_hour));
                if($new_break >= strtotime($start_time)){
                    $ad_breaks[] = date('H:i:s', $new_break);
                }
            }
        }
        return $ad_breaks;
    }
}