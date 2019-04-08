<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Libraries\CampaignDate;
use Vanguard\Services\Traits\TimeBeltTrait;

class TimeBeltByDateRange
{
    use TimeBeltTrait;
    protected $start_date;
    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function getListOfDates()
    {
        $dates = new CampaignDate();
        return $dates->getActualDates($this->start_date, $this->end_date);
    }

    public function getProgramInventory()
    {
        $programs = [];
        foreach ($this->getListOfDates() as $date){
            $program_list_service = new TimeBeltByDate(date('Y-m-d', strtotime($date)));
            $programs[] = [$program_list_service->timeBeltInventory()];
        }
        return array_flatten($programs);
    }

}
