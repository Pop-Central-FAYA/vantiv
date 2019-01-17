<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'amount', 'user_id', 'reference', 'ip_address', 'type', 'card_type', 'status', 'fees', 'message'
    ];

    public $timestamps = false;
}
