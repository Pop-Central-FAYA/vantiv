<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Services\Traits\CalculateTotalSlot;
use Vanguard\Services\Traits\GetTimeBeltInventoryDetails;
use Vanguard\Services\Traits\TimeBeltTrait;

class TimeBeltByDate
{
    use TimeBeltTrait;
    use CalculateTotalSlot;
    use GetTimeBeltInventoryDetails;

    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function getTimeBeltForTheDay()
    {
        return $this->baseQuery()
                    ->where('time_belts.day', strtolower(date('l', strtotime($this->date))))
                    ->get();
    }

    public function timeBeltInventory()
    {
        return $this->getTimeBeltInventory($this->getTimeBeltForTheDay(), $this->date);
    }
}
