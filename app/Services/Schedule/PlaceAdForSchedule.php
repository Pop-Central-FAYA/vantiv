<?php

namespace Vanguard\Services\Schedule;

use Http\Client\Exception\HttpException;
use Matrix\Exception;
use Vanguard\Models\Schedule;

class PlaceAdForSchedule
{
    const TOTAL_SCHEDULABLE_DURATION_FOR_AN_HOUR = 720;
    protected $ad_pattern;
    protected $time_belt_transaction_id;
    protected $time_belt;

    public function __construct($ad_pattern, $time_belt_transaction_id, $time_belt)
    {
        $this->ad_pattern = $ad_pattern;
        $this->time_belt_transaction_id = $time_belt_transaction_id;
        $this->time_belt = $time_belt;
    }

    public function run()
    {
        $per_break_duration = self::TOTAL_SCHEDULABLE_DURATION_FOR_AN_HOUR / $this->ad_pattern;
        $scheduled_list = $this->buildScheduledList();
        foreach ($scheduled_list as $schedule){
            try{
                if($per_break_duration >= ($schedule['duration'] + $this->time_belt->duration)){
                    $order = $schedule['total_order'] + 1;
                    return $this->updateTimeBeltTransactions($order, $schedule['ad_break'], $this->time_belt_transaction_id);
                }
            }catch (\Exception $e){
                return null;
            }

        }
    }

    private function updateTimeBeltTransactions($order, $play_out, $time_belt_transaction_id)
    {
        $schedule = Schedule::where('id', $time_belt_transaction_id)->first();
        $schedule->order = $order;
        $schedule->playout_hour = $play_out;
        $schedule->save();
        return $schedule;
    }

    /**
     * @return array
     * Another thing to consider is if the program is not starting at the top hour
     */
    private function getHoursInTheProgram()
    {
        return \DB::table('time_belts')
            ->selectRaw("hour(start_time) as playout_hours")
            ->where('media_program_id', $this->time_belt->media_program_id)
            ->groupBy(\DB::raw('hour(start_time)'))
            ->get();
    }

    private function buildScheduledList()
    {
        $playout_iterator = $this->playoutHourIterator();
        $scheduled_list = [];
        foreach ($playout_iterator as $playout_hour){
            $get_schedule = \DB::table('time_belt_transactions')
                            ->selectRaw("SUM(duration) as total_duration, playout_hour, COUNT(id) as number_of_scheduled")
                            ->where([
                                ['playout_date', $this->time_belt->playout_date],
                                ['company_id', $this->time_belt->company_id],
                                ['media_program_id', $this->time_belt->media_program_id]
                            ])
                            ->whereTime('playout_hour', $playout_hour)
                            ->groupBy('playout_hour')
                            ->get();
            if(count($get_schedule) != 0){
                $duration = $get_schedule[0]->total_duration;
                $number_of_scheduled = $get_schedule[0]->number_of_scheduled;
            }else{
                $duration = 0;
                $number_of_scheduled = 0;
            }
            $scheduled_list[] = [
                'ad_break' => $playout_hour,
                'duration' => $duration,
                'total_order' => $number_of_scheduled
            ];
        }
        return $scheduled_list;
    }

    private function playoutHourIterator()
    {
        $playout_hours = $this->getHoursInTheProgram();
        $adbreak_mintes = 60 / $this->ad_pattern;
        $ad_breaks = [];
        foreach ($playout_hours as $playout_hour){
            $play_out_hour = $playout_hour->playout_hours.':00:00';
            for ($i = 0; $i < $this->ad_pattern; $i++){
                $added_minutes = $i * $adbreak_mintes;
                $ad_breaks[] = date('H:i:s', strtotime('+'.$added_minutes.' minutes', strtotime($play_out_hour)));
            }
        }
        return $ad_breaks;
    }
}