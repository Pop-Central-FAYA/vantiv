<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;
use Vanguard\CustomisePlan\MediaPlanSugestion;
class MediaPlan extends Model
{
    protected $table = 'media_plans';

    public function media_plan_suggestions()
    {
        return $this->hasMany(MediaPlanSugestion::class);
    }
}
