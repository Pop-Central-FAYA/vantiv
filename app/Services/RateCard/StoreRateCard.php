<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Models\RateCard;

class StoreRateCard
{
    protected $user_id;
    protected $company_id;
    protected $day_id;
    protected $hourly_range_id;

    public function __construct($user_id, $company_id, $day_id, $hourly_range_id)
    {
        $this->user_id = $user_id;
        $this->company_id = $company_id;
        $this->day_id = $day_id;
        $this->hourly_range_id = $hourly_range_id;
    }

    public function storeRateCard()
    {
        $ratecard = new RateCard();
        $ratecard->user_id = $this->user_id;
        $ratecard->broadcaster = $this->company_id;
        $ratecard->day = $this->day_id;
        $ratecard->hourly_range_id = $this->hourly_range_id;
        $ratecard->company_id = $this->company_id;
        $ratecard->save();
        return $ratecard;
    }
}
