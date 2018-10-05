<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Adslot extends Model
{
    protected $connection = 'api_db';
    protected $table = 'adslots';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function get_rate_card()
    {
        return $this->belongsTo(RateCard::class, 'rate_card');
    }

    public function day_part()
    {
        return $this->belongsTo(DayPart::class, 'day_parts');
    }
}
