<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class AdslotPercentage extends Model
{
    protected $table = 'adslotPercentages';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'adslot_id', 'price_60', 'price_45', 'price_30', 'price_15', 'percentage'
    ];
}
