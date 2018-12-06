<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountClass extends Model
{
    protected $table = 'discount_classes';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'class', 'status'
    ];
}
