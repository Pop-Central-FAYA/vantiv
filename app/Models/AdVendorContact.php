<?php

namespace Vanguard\Models;

class AdVendorContact extends Base
{

    protected $fillable = [
        'ad_vendor_id', 'first_name', 'last_name', 'email',
        'phone_number', 'is_primary', 'created_by'
    ];

    public function ad_vendor()
    {
        return $this->belongsTo('Vanguard\Models\AdVendor');
    }
}
