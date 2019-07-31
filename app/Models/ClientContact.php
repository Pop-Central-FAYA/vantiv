<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class ClientContact extends Base
{
    protected $primaryKey = 'id';
    protected $fillable = ['client_id', 'first_name', 'last_name', 'email', 'phone_number', 'is_primary'];

}
