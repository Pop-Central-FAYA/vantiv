<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class RateCard extends Base
{
    protected $table = 'rateCards';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['user_id', 'broadcaster', 'hourly_range_id', 'day', 'is_airing', 'status', 'company_id'];

    public $timestamps = null;

    protected $dates = ['time_created', 'time_modified'];

    public function adslots()
    {
        return $this->hasMany(Adslot::class, 'rate_card', 'id');
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
