<?php

namespace Vanguard\Services\Traits;

use Vanguard\Libraries\Utilities;

trait SplitTimeRange
{
    public function splitTimeRangeByBase($start_time, $end_time, $populate)
    {
        $start_time_array = [];
        $start_time = strtotime(Utilities::removeSpace($start_time));
        $end_time = strtotime(Utilities::removeSpace($end_time));
        $base_time_seconds = 15 * 60;
        $time_difference_in_seconds = ($end_time - $start_time);
        $number_of_iterations = $time_difference_in_seconds / $base_time_seconds;
        for ($x = 0; $populate ? $x <= $number_of_iterations : $x < $number_of_iterations ; $x++){
            $new_iteration = $base_time_seconds * $x;
            $new_iteration_for_end_time = $base_time_seconds * ($x+1);
            $new_start_time = date('H:i', ($start_time + $new_iteration));
            $new_end_time = date('H:i', ($start_time + $new_iteration_for_end_time));
            $start_time_array[] = [
                'start_time' => $new_start_time,
                'end_time' => $new_end_time
            ];
        }
        return $start_time_array;
    }
}
