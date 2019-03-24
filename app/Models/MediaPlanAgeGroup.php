<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPlanAgeGroup extends Model
{
    protected $fillable = ['media_plan_id', 'min_age', 'max_age'];
}
