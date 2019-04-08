<?php

namespace Vanguard\Services\Traits;

trait GetStartTimeInTimeRange
{
    public function getStartTimeIteration($time_range)
    {
        $start_time_array = [];
        $time_array = explode('-',$time_range);
        $start_time = strtotime($time_array[0]);
        $end_time = strtotime($time_array[1]);
        $base_time_seconds = 15 * 60;
        $time_difference_in_seconds = ($end_time - $start_time);
        $number_of_iterations = $time_difference_in_seconds / $base_time_seconds;
        for ($x = 0; $x <= $number_of_iterations; $x++){
            $new_iteration = $base_time_seconds * $x;
            $new_start_time = date('H:i', ($start_time + $new_iteration));
            $start_time_array[] = $new_start_time;
        }
        return $start_time_array;
    }
}
