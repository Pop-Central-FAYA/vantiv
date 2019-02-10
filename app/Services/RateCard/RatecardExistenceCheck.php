<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Models\RateCard;

class RatecardExistenceCheck
{
    protected $company_id;
    protected $hourly_range_id;
    protected $day_id;

    public function __construct($company_id, $hourly_range_id, $day_id)
    {
        $this->company_id = $company_id;
        $this->hourly_range_id = $hourly_range_id;
        $this->day_id = $day_id;
    }

    public function checkIfRatecardExists()
    {
        return RateCard::where([
            ['company_id', $this->company_id],
            ['day', $this->day_id],
            ['hourly_range_id', $this->hourly_range_id]
        ])
        ->first();
    }
}
