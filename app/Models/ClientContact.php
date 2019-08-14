<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\Client;

class ClientContact extends Base
{
    protected $fillable = ['client_id', 'first_name', 'last_name', 'email', 'phone_number', 'is_primary'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
