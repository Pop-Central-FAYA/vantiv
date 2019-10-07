<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vanguard\Services\MediaPlan\FormatMediaPlan;

class MediaPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $format = new FormatMediaPlan();
        return [
            'id' => $this->id,
            'plan_id' => $this->id,
            'campaign_name' => $this->campaign_name,
            'campaign_duration' => date('M j, Y', strtotime($this->start_date)).' - '.date('M j, Y', strtotime($this->end_date)),
            'date_created' => date('M j, Y', strtotime($this->created_at)),
            'start_date' => date('M j, Y', strtotime($this->start_date)),
            'end_date' => date('M j, Y', strtotime($this->end_date)),
            'media_type' => $this->media_type,
            'redirect_url' => $format->generateRedirectUrl($this),
            'status' => $this->status,
            'str_status' => $format->getStatusHtml($this),
            'client' => $this->client,
            'client_id' => $this->client_id,
            'brand_id' => $this->brand_id,
            'product_name' => $this->product_name,
            'net_media_cost' => $this->net_media_cost,
            'brand' => $this->brand,
            'total_insertions' => $this->total_insertions,
            'gross_impressions' => $this->gross_impressions,
            'total_grp' => $this->total_grp,
            'net_reach' => $this->net_reach,
            'avg_frequency' => $this->avg_frequency,
            'cpm' => $this->cpm,
            'cpp' => $this->cpp,
            'routes' => [
                'summary' => [
                    'back' => route('agency.media_plan.create', ['id'=>$this->id], false),
                    'change_status' => route('agency.media_plan.change_status'),
                    'export' => route('agency.media_plan.export', ['id'=>$this->id], false),
                    'approval' => route('agency.media_plan.get_approval')
                ],
                'insertions' => [
                    'back_action' => route('agency.media_plan.customize', ['id'=>$this->id], false),
                    'next_action' => route('agency.media_plan.summary', ['id'=>$this->id], false),
                    'save_action' => route('agency.media_plan.submit.finish_plan')
                ],
                'suggestions' => [
                    'back_action' => route('agency.media_plans', [], false),
                    'next_action' => route('agency.media_plan.create', ['id' => $this->id], false),
                    'save_action' => route('agency.media_plan.select_suggestions', ['id' => $this->id], false),
                    'new_ratings_action' => route('reach.get', ['plan_id' => $this->id], false),
                    'timebelt_ratings' => route('reach.get-timebelts', ['plan_id' => $this->id], false)
                ],
                'comments' => [
                    "all" => route('agency.media_plan.comment.all', ['id'=>$this->id], false),
                    "store" => route('agency.media_plan.comment.store', ['id'=>$this->id], false),
                ]
            ]
        ];
    }
}
