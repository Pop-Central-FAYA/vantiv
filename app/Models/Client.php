<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\ClientContact;
use Vanguard\Models\Brand;
use EloquentFilter\Filterable;

use Vanguard\Models\Campaign;

class Client extends Base
{
    use Filterable;
    protected $fillable = ['name', 'image_url', 'created_by', 'company_id', 'street_address', 'city', 'state','nationality'];
   
    public function modelFilter()
    {
        return $this->provideFilter(\Vanguard\ModelFilters\ClientFilter::class);
    }
    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
