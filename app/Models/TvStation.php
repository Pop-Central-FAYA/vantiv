<?php

namespace Vanguard\Models;

class TvStation extends Base
{

    protected $fillable = ['name', 'publisher_id', 'type', 'state', 'city', 'region'];

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }



}