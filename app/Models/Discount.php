<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'broadcaster', 'discount_type', 'discount_class', 'percentage_value',
        'percentage_start_date', 'percentage_stop_date', 'value', 'value_start_date', 'value_stop_date',
        'discount_type_value', 'discount_type_sub_value', 'status'
    ];
}
