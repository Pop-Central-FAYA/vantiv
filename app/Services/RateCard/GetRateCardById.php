<?php

namespace Vanguard\Services\RateCard;

use Vanguard\Models\RateCard\Ratecard;

class GetRateCardById
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getRateCardById()
    {
        return RateCard::where('id', $this->id)->first();
    }
}
