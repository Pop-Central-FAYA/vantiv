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
            $this->plan_status = [MediaPlanStatus::PENDING,MediaPlanStatus::SUGGESTED,
                                MediaPlanStatus::SELECTED,MediaPlanStatus::IN_REVIEW];
        } elseif ($status == MediaPlanStatus::APPROVED || $status == MediaPlanStatus::DECLINED) {
            $this->plan_status = [$status];
        } 
        else {
            $this->plan_status = [MediaPlanStatus::PENDING,MediaPlanStatus::SUGGESTED,MediaPlanStatus::SELECTED,
                                MediaPlanStatus::APPROVED,MediaPlanStatus::DECLINED,MediaPlanStatus::IN_REVIEW];
        }
        $this->company_id = $company_id;  
    }

    public function run()
    {
        return $this->fetchMediaPlans();
    }

    public function fetchMediaPlans()
    {
        $status = $this->plan_status;
        $plans =  MediaPlan::where('company_id', $this->company_id)
                    ->whereIn('status', $status)
                    ->get();
        $plans = $this->reformatMediaPlans($plans);
        return $plans;
    }

    public function reformatMediaPlans($media_plans)
    {
        $plans = [];
        foreach ($media_plans as $plan)
        {
            $start_date = strtotime($plan->start_date);
            $end_date = strtotime($plan->end_date);
            $plans[] = [
                'plan_id' => $plan->id,
                'campaign_name' => $plan->campaign_name,
                'campaign_duration' => date('M j, Y', $start_date).' - '.date('M j, Y', $end_date),
                'date_created' => date('M j, Y', strtotime($plan->created_at)),
                'start_date' => date('M j, Y', strtotime($plan->start_date)),
                'end_date' => date('M j, Y', strtotime($plan->end_date)),
                'media_type' => $plan->media_type,
                'redirect_url' => $this->generateRedirectUrl($plan),
                'status' => $this->getStatusHtml($plan),
            ];
        }
        return $plans;
    }

    public function generateRedirectUrl($media_plan)
    {
        if ($media_plan->status === MediaPlanStatus::APPROVED || 
            $media_plan->status === MediaPlanStatus::DECLINED|| 
            $media_plan->status === MediaPlanStatus::IN_REVIEW) {
            return route('agency.media_plan.summary',['id'=>$media_plan->id]);
        } elseif ($media_plan->status === MediaPlanStatus::PENDING || 
                  $media_plan->status === MediaPlanStatus::SUGGESTED) {
            return route('agency.media_plan.customize',['id'=>$media_plan->id]);
        } elseif ($media_plan->status === MediaPlanStatus::SELECTED) {
            return route('agency.media_plan.create',['id'=>$media_plan->id]);
        } else {
            return route('agency.media_plans');
        }
    }
    
    public function getStatusHtml($media_plan)
    {
        if ($media_plan->status === MediaPlanStatus::APPROVED || $media_plan->status === MediaPlanStatus::DECLINED) {
            return '<span class="span_state status_success">'.MediaPlanStatus::APPROVED.'</span>';
        } elseif ($media_plan->status === MediaPlanStatus::DECLINED) {
            return '<span class="span_state status_danger">'.MediaPlanStatus::DECLINED.'</span>';
        } elseif ($media_plan->status === MediaPlanStatus::PENDING || 
                  $media_plan->status === MediaPlanStatus::SUGGESTED || 
                  $media_plan->status === MediaPlanStatus::SELECTED  || 
                  $media_plan->status === MediaPlanStatus::IN_REVIEW) {
            return '<span class="span_state status_pending">'.MediaPlanStatus::PENDING.'</span>';
        } else {
            return '<span class="span_state status_danger">File Error</span>';
        }
    }
}