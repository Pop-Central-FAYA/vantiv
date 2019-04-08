<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Models\TimeBelt;
use Vanguard\Services\Traits\ListDayTrait;
use Vanguard\Services\Traits\SplitTimeRange;

class PopulateTimeBelt
{
    protected $publisher_id;
    use SplitTimeRange;
    use ListDayTrait;

    public function __construct($publisher_id)
    {
        $this->publisher_id = $publisher_id;
    }

    private function checkTimeBeltExistence()
    {
        return TimeBelt::where('station_id', $this->publisher_id)->first();
    }

    public function populateTimeBelt()
    {
        if(!$this->checkTimeBeltExistence()){
            $time_belts = $this->splitTimeRangeByBase('00:00', '23:45', true);
            foreach ($this->listDays() as $day){
                foreach ($time_belts as $time_belt){
                    $time_belt_model = new TimeBelt();
                    $time_belt_model->start_time = $time_belt['start_time'];
                    $time_belt_model->end_time = $time_belt['end_time'];
                    $time_belt_model->day = $day;
                    $time_belt_model->station_id = $this->publisher_id;
                    $time_belt_model->save();
                }
            }
            return 'success';
        }else{
            return 'error';
        }

    }
}
