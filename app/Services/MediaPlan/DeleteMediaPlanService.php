<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Vanguard\Libraries\Enum\MediaPlanStatus;

class DeleteMediaPlanService
{
    protected $media_plan;

    public function __construct($media_plan)
    {
        $this->media_plan = $media_plan;  
    }

    public function run()
    {
        return $this->media_plan->delete();
    }
}