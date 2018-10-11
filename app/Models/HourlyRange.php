<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class HourlyRange extends Model
{
    protected $connection = 'api_db';
    protected $table = 'hourlyRanges';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function rate_card()
    {
        return $this->hasMany(RateCard::class);
    }
}
