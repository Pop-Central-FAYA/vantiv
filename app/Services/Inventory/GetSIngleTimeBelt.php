<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Models\TimeBelt;

class GetSIngleTimeBelt
{
    /**
     * this service takes in start_time, day and station id to get a station
     */
    protected $start_time;
    protected $day;
    protected $station_id;

    public function __construct($start_time, $day, $station_id)
    {
        $this->start_time = $start_time;
        $this->day = $day;
        $this->station_id = $station_id;
    }

    public function getTimeBelt()
    {
        $start_time = $this->start_time.':00';
        return TimeBelt::where([
                ['day', $this->day],
                ['station_id', $this->station_id],
            ])
            ->whereTime('start_time', $start_time)
            ->first();
    }
}
