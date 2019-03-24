<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPlanChannel extends Model
{
    protected $fillable = ['media_plan_id', 'channel', 'budget', 'target_reach', 'material_length'];
}
