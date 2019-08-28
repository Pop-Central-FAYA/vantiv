<?php

namespace Vanguard\Models;

use EloquentFilter\Filterable;

class AdVendor extends Base
{
    use Filterable;

    protected $fillable = [
        'company_id', 'name', 'street_address',
        'city', 'state', 'country', 'created_by'
    ];

    public function modelFilter()
    {
        return $this->provideFilter(\Vanguard\ModelFilters\AdVendorFilter::class);
    }

    public function contacts()
    {
        return $this->hasMany(AdVendorContact::class);
    }

    public function publishers()
    {
        return $this->belongsToMany(Publisher::class);
    }
}
