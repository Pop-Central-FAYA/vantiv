<?php

namespace Vanguard\Models;

use Vanguard\Models\Ratecard\Ratecard;

class MediaProgram extends Base
{
    protected $fillable = ['name','status', 'company_id', 'slug', 'start_date', 'end_date', 'rate_card_id', 'program_vendor_id'];

    public function time_belts()
    {
        return $this->hasMany(TimeBelt::class);
    }

    public function time_belt_transactions()
    {
        return $this->hasMany(TimeBeltTransaction::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function rate_card()
    {
        return $this->belongsTo(Ratecard::class);
    }
}
