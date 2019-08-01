<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\ClientContact;

class Client extends Base
{
    protected $fillable = ['name', 'brand', 'image_url', 'status', 'created_by', 'company_id', 'street_address', 'city', 'state','nationality'];

    public function client_contact()
    {
        return $this->hasMany(ClientContact::class);
    }
}
