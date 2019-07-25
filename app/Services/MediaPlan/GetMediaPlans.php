<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Yajra\DataTables\DataTables;
use Auth;

class GetMediaPlans
{
    protected $plan_status;
    protected $planners_id;

    public function __construct($status='', $planners_id=[])
    {
        if ($status == "pending") {
            $this->plan_status = ['Pending','Suggested','Selected'];
        } elseif ($status == "approved" || $status == "declined") {
            $this->plan_status = [$status];
        } 
        else {
            $this->plan_status = ['Pending','Suggested','Selected','Approved','Declined'];
        }
        $this->planners_id = $planners_id;  
    }

    public function run()
    {
        return $this->fetchMediaPlans();
    }

    public function fetchMediaPlans()
    {
        $status = $this->plan_status;
        $planners_id = $this->planners_id;
        $plans =  MediaPlan::whereIn('status', $status)
                    ->when($planners_id, function ($query, $planners_id) {
                        $query->whereIn('planner_id', $planners_id);
                    })
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
        if ($media_plan->status === "Approved" || $media_plan->status === "Declined") {
            return route('agency.media_plan.summary',['id'=>$media_plan->id]);
        } elseif ($media_plan->status === "Pending" || $media_plan->status === "Suggested") {
            return route('agency.media_plan.customize',['id'=>$media_plan->id]);
        } elseif ($media_plan->status === "Selected") {
            return route('agency.media_plan.create',['id'=>$media_plan->id]);
        } else {
            return route('agency.media_plans');
        }
    }
    
    public function getStatusHtml($media_plan)
    {
        if ($media_plan->status === "Approved" || $media_plan->status === "Declined") {
            return '<span class="span_state status_success">Approved</span>';
        } elseif ($media_plan->status === "Declined") {
            return '<span class="span_state status_danger">Declined</span>';
        } elseif ($media_plan->status === "Pending" || $media_plan->status === "Suggested" || $media_plan->status === "Suggested") {
            return '<span class="span_state status_pending">Pending</span>';
        } else {
            return '<span class="span_state status_danger">File Error</span>';
        }
    }
}