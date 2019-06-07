<?php

namespace Vanguard\Services\Schedule;

use Vanguard\Models\Schedule;

class GetAdsPerHour
{
    /**
     * This service is responsible for getting all the ads that have been scheduled within an hour.
     * It takes in the playout_dates, company_id, and playout_hour as an argument and returns all ads scheduled
     */

    protected $playout_date;
    protected $playout_hour;
    protected $company_id;

    public function __construct($playout_date, $playout_hour, $company_id)
    {
        $this->playout_date = $playout_date;
        $this->playout_hour = $playout_hour;
        $this->company_id = $company_id;
    }

    public function run()
    {
        return Schedule::where([
            ['playout_date', $this->playout_date],
            ['playout_hour', $this->playout_hour],
            ['company_id', $this->company_id]
        ])->get();
    }
}