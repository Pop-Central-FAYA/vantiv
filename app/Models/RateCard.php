<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class RateCard extends Model
{
    protected $connection = 'api_db';
    protected $table = 'rateCards';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function adslots()
    {
        return $this->hasMany(Adslot::class);
    }

    public function get_day()
    {
        return $this->belongsTo(Day::class, 'day');
    }

    public function hourly_range()
    {
        return $this->belongsTo(HourlyRange::class, 'hourly_range_id');
    }


}
