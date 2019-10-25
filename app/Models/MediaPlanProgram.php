<?php

namespace Vanguard\Models;

class MediaPlanProgram extends Base
{
    protected $fillable = ['program_name', 'start_time', 'end_time', 'station', 'day'];

    public function media_plan_program_ratings()
    {
        return $this->hasMany(MediaPlanProgramRating::class);
    }

    public function station()
    {
        return $this->belongsTo(TvStation::class);
    }
}
