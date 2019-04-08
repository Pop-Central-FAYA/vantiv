<?php

namespace Vanguard\Services\Traits;

trait FormatTimeBelt
{
    /**
     * @param $time
     * this trait takes in time and format it to a format for out putting
     * like 00:00:00 to 00h00
     */
    public function formatTimeBelt($time)
    {
        $exploded_time = explode(':', $time);
        return $exploded_time[0].'h'.$exploded_time[1];
    }
}
