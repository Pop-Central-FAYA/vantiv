<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Vanguard\Libraries\Enum\MediaPlanStatus;

class GetMediaPlans
{
    protected $plan_status;
    protected $company_id;

    public function __construct($status='', $company_id)
    {
        if ($status == MediaPlanStatus::PENDING) {
            $this->plan_status = [MediaPlanStatus::PENDING, MediaPlanStatus::IN_REVIEW, MediaPlanStatus::FINALIZED];
        } elseif ($status == MediaPlanStatus::APPROVED || $status == MediaPlanStatus::REJECTED) {
            $this->plan_status = [$status];
        } else {
            $this->plan_status = [MediaPlanStatus::PENDING,MediaPlanStatus::CONVERTED,MediaPlanStatus::FINALIZED,
                                MediaPlanStatus::APPROVED,MediaPlanStatus::REJECTED,MediaPlanStatus::IN_REVIEW];
        }
        $this->company_id = $company_id;  
    }

    public function run()
    {
        return $this->fetchMediaPlans();
    }

    public function fetchMediaPlans()
    {
        return MediaPlan::with(['brand'])->where('company_id', $this->company_id)
                    ->whereIn('status', $this->plan_status)
                    ->get();
    }
}