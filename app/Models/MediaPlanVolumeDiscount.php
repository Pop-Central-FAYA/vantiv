<?php

namespace Vanguard\Models;

class MediaPlanVolumeDiscount extends Base
{
    protected $fillable = ['station', 'agency_id', 'discount'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'agency_id', 'id');
    }
}
