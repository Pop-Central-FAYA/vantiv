<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPlanSuggestion extends Model
{
    protected $fillable = ['media_plan_id', 'station', 'program', 'start_time', 'end_time', 'total_audience', 'total_spots', 'status'];
}
