<?php

namespace Vanguard;


use Illuminate\Database\Eloquent\Model;
use Vanguard\CustomisePlan\MediaPlan;

class MediaPlanSugestion extends Model
{
    protected $table = 'media_plan_suggestions';

    public function media_plan()
    {
        return $this->belongsTo(MediaPlan::class);
    }
}
