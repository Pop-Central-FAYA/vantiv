<?php

namespace Vanguard\Models;


class AdslotPrice extends Base
{
    protected $table = 'adslotPrices';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = false;

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'adslot_id', 'price_60', 'price_45', 'price_30', 'price_15'
    ];
}
