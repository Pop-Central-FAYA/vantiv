<?php

namespace Vanguard\Services\Schedule;

class AdPatternSchedule
{
    protected $preselected_time_belt;
    protected $ad_pattern;

    public function __construct($preselected_time_belt, $ad_pattern)
    {
        $this->preselected_time_belt = $preselected_time_belt;
        $this->ad_pattern = $ad_pattern;
    }
}