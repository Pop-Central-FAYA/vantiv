<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Vanguard\Libraries\Enum\MediaPlanStatus;

class FormatMediaPlan
{
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