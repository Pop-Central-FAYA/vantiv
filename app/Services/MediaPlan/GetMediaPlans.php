<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Yajra\DataTables\DataTables;
use Auth;

class GetMediaPlans
{
    public function __construct()
    {
        
    }

    public function run()
    {
        return $this->mediaPlanDataToDatatables();
    }

    public function mediaPlanDataToDatatables()
    {
        $datatables = new DataTables();

        $plans = $this->fetchAllMediaPlans();

        $plans = $this->getMediaPlanDatatables($plans);

        return $datatables->collection($plans)
            ->editColumn('campaign_name', function ($plans){
                // 'Suggested','Selected','Pending','Approved','Declined'
                if($plans['status'] === "Approved" OR $plans['status'] === "Pending"){
                    return '<a href="'.route('agency.media_plan.summary', ['id'=>$plans['plan_id']]).'">'.$plans['campaign_name'].'</a>';
                }elseif($plans['status'] === "Declined" && $plans['status'] === "Selected") {
                    //redirect to the review suggested plan page
                    return '<a href="'.route('agency.media_plan.create', ['id'=>$plans['plan_id']]).'">'.$plans['campaign_name'].'</a>';
                }elseif($plans['status'] === "Suggested") {
                    //redirect to the select suggestions page
                    return '<a href="'.route('agency.media_plan.customize', ['id'=>$plans['plan_id']]).'">'.$plans['campaign_name'].'</a>';
                }else {
                    return '<a href="#">'.$plans['campaign_name'].'</a>';
                }
            })
            ->editColumn('status', function ($plans){
                if($plans['status'] === "Approved"){
                    return '<span class="span_state status_success">Approved</span>';
                }elseif ($plans['status'] === "Pending" OR $plans['status'] === "Suggested" OR $plans['status'] === "Selected"){
                    return '<span class="span_state status_pending">Pending</span>';
                }elseif ($plans['status'] === 'Declined'){
                    return '<span class="span_state status_danger">Declined</span>';
                }else {
                    return '<span class="span_state status_danger">File Errors</span>';
                }
            })
            ->rawColumns(['status' => 'status', 'campaign_name' => 'campaign_name'])
            ->addIndexColumn()
            ->make(true);
    }

    public function fetchAllMediaPlans()
    {
        return MediaPlan::where('planner_id', Auth::id())->latest()->get();
    }

    public function getMediaPlanDatatables($media_plans)
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
                'start_date' => date('M j, Y', $start_date),
                'end_date' => date('M j, Y', $end_date),
                'media_type' => $plan->media_type,
                // 'budget' => number_format($plan->budget, 2),
                'status' => $plan->status,
            ];
        }
        return $plans;
    }

    public function approvedPlans()
    {
        return MediaPlan::where('planner_id', Auth::id())->where('status', 'Approved')->count();
    }

    public function declinedPlans()
    {
        return MediaPlan::where('planner_id', Auth::id())->where('status', 'Declined')->count();
    }

    public function pendingPlans()
    {
        return MediaPlan::where('planner_id', Auth::id())->whereIn('status', ['Pending','Suggested','Selected'])->count();
    }
}