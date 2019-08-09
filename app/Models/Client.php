<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\ClientContact;
use Vanguard\Models\Brand;

class Client extends Base
{
    protected $fillable = ['name', 'brand', 'image_url', 'created_by', 'company_id', 'street_address', 'city', 'state','nationality'];

    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }
}
