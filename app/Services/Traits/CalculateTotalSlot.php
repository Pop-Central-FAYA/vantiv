<?php

namespace Vanguard\Services\Traits;

trait CalculateTotalSlot
{
    /**
     * This trait takes in the end and start time of a time belt and calculate the
     * ad duration in between using 15 minute block as the base duration
     */
    public function calculateTotalSlot($end_time, $start_time)
    {
            $time_difference_seconds = strtotime($end_time) - strtotime($start_time);
            return ($time_difference_seconds / 5);
    }
}
