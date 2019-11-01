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

    public function programs()
    {
        return $this->belongsToMany(MediaPlanProgram::class, 'program_ad_vendor', 'program_id', 'ad_vendor_id');
    }

    /**
     * Get association with the campaign time belts through the ad_vendor_id column
     */
    public function time_belts()
    {
        return $this->hasMany(CampaignTimeBelt::class);
    }

    public function mpos()
    {
        return $this->hasMany(CampaignMpo::class);
    }
}
