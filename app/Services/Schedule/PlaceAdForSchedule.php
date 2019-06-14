<?php

namespace Vanguard\Services\Schedule;

class PlaceAdForSchedule
{
    const TOTAL_SCHEDULABLE_DURATION_FOR_AN_HOUR = 720;
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

    /**
     * @return array
     * Another thing to consider is if the program is not starting at the top hour
     */
    private function getHoursInTheProgram()
    {
        return \DB::table('time_belts')
                    ->selectRaw("hour(start_time) as playout_hours")
                    ->where('media_program_id', $this->media_program_id)
                    ->groupBy(\DB::raw('hour(start_time)'))
                    ->get()
                    ->toArray();
    }

    private function determineWhereAdsFit()
    {
        $per_break_duration = self::TOTAL_SCHEDULABLE_DURATION_FOR_AN_HOUR / $this->ad_pattern;
        $scheduled_list = $this->buildScheduledList();
        foreach ($scheduled_list as $schedule){
            if($per_break_duration >= ($schedule['duration'] + $this->duration)){
                $new_order = $schedule['total_order'] + 1;

            }
        }
    }

    private function buildScheduledList()
    {
        $playout_iterator = $this->playoutHourIterator();
        $scheduled_list = [];
        foreach ($playout_iterator as $playout_hour){
            $get_schedule = \DB::table('time_belt_transactions')
                            ->selectRaw("SUM(duration) as total_duration, playout_hour, COUNT(id) as number_of_scheduled")
                            ->where([
                                ['playout_hour', $playout_hour],
                                ['playout_date', $this->playout_date],
                                ['company_id', $this->company_id],
                                ['media_program_id', $this->media_program_id]
                            ])
                            ->groupBy('playout_hour')
                            ->get();
            if($get_schedule){
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
            for ($i = 0; $i < $this->ad_pattern; $i++){
                $added_minutes = $i * $minutes_in_adbreak;
                $ad_breaks[] = date('H:i:s', strtotime('+'.$added_minutes.' minutes', strtotime($playout_hour)));
            }
        }
        return $ad_breaks;
    }
}