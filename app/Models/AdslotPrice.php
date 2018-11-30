<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class AdslotPrice extends Model
{
    protected $table = 'adslotPrices';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'adslot_id', 'price_60', 'price_45', 'price_30', 'price_15'
    ];
}
