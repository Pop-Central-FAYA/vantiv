<?php

namespace Vanguard\Services\Traits;

use Vanguard\Libraries\DayPartList;


trait GetDayPartTrait
{
    public function getDayPart($time_belt_start_time)
    {
        $day_parts = DayPartList::DAYPARTLIST;
        $time_belt_stamp = strtotime($time_belt_start_time);
        foreach($day_parts as $key => $day_part){
            $day_part_start_stamp = strtotime($day_part[0].':00');
            $day_part_end_stamp = strtotime($day_part[count($day_part) - 1].':00');
            if($time_belt_stamp >= $day_part_start_stamp && $time_belt_stamp <= $day_part_end_stamp){
                return ['name' => $key, 'range' => $day_part];
            }
        }
    }
}