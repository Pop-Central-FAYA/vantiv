<?php

namespace Vanguard\Models;


class TimeBelt extends Base
{
    protected $fillable = ['start_time', 'end_time', 'day', 'media_program_id', 'station_id'];

    public function media_program()
    {
        return $this->belongsTo(MediaProgram::class);
    }

    public function time_belt_transactions()
    {
        return $this->hasMany(TimeBeltTransaction::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'station_id', 'id');
    }
}
