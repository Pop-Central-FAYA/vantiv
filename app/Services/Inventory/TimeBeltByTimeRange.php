<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Services\Traits\CalculateTotalSlot;
use Vanguard\Services\Traits\GetStartTimeInTimeRange;
use Vanguard\Services\Traits\GetTimeBeltInventoryDetails;
use Vanguard\Services\Traits\TimeBeltTrait;

class TimeBeltByTimeRange
{
    use TimeBeltTrait;
    use GetStartTimeInTimeRange;
    use CalculateTotalSlot;
    use GetTimeBeltInventoryDetails;
    protected $date;
    protected $company_id;
    protected $time_range;

    public function __construct($date, $company_id, $time_range)
    {
        $this->date = $date;
        $this->company_id = $company_id;
        $this->time_range = $time_range;
    }

    /**
     * This methods takes in the time range and split
     * it up by 15 minutes which is the base
     */

    public function getTimeBelt()
    {
        return $this->baseQuery()
                    ->where([
                        ['time_belts.company_id', $this->company_id],
                        ['time_belts.day', strtolower(date('l', strtotime($this->date)))]
                    ])
                    ->whereIn('time_belts.start_time', $this->getStartTimeIteration($this->time_range))
                    ->get();
    }

    public function programInventory()
    {
        return $this->getTimeBeltInventory($this->getTimeBelt(), $this->date);
    }
}
