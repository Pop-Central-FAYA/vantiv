<?php

namespace Vanguard\Models;

class RateCardDuration extends Base
{
    protected $fillable = ['rate_card_id', 'duration', 'price'];

    public function rate_card()
    {
        return $this->belongsTo(\Vanguard\Models\Ratecard\Ratecard::class);
    }
}
