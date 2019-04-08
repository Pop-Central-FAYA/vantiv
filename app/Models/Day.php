<?php

namespace Vanguard\Models;


class Day extends Base
{
    protected $table = 'days';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['day'];

    protected $dates = ['time_created', 'time_modified'];

    public $timestamps = false;

    public function rate_card()
    {
        return $this->hasMany(RateCard::class);
    }
}
