<?php

namespace Vanguard\Models;


class Adslot extends Base
{
    protected $table = 'adslots';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['rate_card', 'target_audience', 'day_parts', 'region', 'from_to_time', 'min_age', 'max_age',
        'status', 'broadcaster', 'is_available', 'time_difference', 'time_used', 'channels', 'company_id'];

    protected $dates = ['time_created', 'time_modified'];

    public $timestamps = false;

    public function files()
    {
        return $this->hasMany(SelectedAdslot::class);
    }

    public function get_rate_card()
    {
        return $this->belongsTo(RateCard::class, 'rate_card');
    }

    public function day_part()
    {
        return $this->belongsTo(DayPart::class, 'day_parts');
    }

    public function rate_card()
    {
        return $this->belongsTo(RateCard::class);
    }
}
