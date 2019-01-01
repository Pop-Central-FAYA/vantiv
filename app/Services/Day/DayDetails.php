<?php

namespace Vanguard\Services\Day;

use Vanguard\Libraries\Utilities;

class DayDetails
{
    protected $day_id;
    protected $day_name;

    public function __construct($day_id, $day_name)
    {
        $this->day_id = $day_id;
        $this->day_name = $day_name;
    }

    public function getDayDetails()
    {
        return Utilities::switch_db('api')->table('days')
                    ->when($this->day_name, function ($query) {
                        return $query->where('day', $this->day_name);
                    })
                    ->when($this->day_id, function($query) {
                        return $query->where('id', $this->day_id);
                    })
                    ->first();
    }
}
