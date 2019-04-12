<?php

namespace Vanguard\Models;

class MediaPlanSuggestions extends Base
{
    protected $table = 'media_plan_suggestions';

    public function media_plan()
    {
        return $this->belongsTo(MediaPlan::class);
    }
}
