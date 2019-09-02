<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>  $this->campaign_reference,
            'campaign_id' =>  $this->id,
            'name' =>  $this->name,
            'product' =>  $this->product,
            'brand' => ucfirst($this->brand['name']),
            'date_created' => date('Y-m-d', strtotime($this->time_created)),
            'start_date' => date('Y-m-d', strtotime($this->start_date)),
            'end_date' => date('Y-m-d', strtotime($this->stop_date)),
            'adslots' => $this->ad_slots,
            'budget' => number_format($this->budget,2),
            'status' => $this->getCampaignStatusHtml($request),
            'redirect_url' => $this->generateRedirectUrl($request),
            'station' => ''
        ];
    }

    public function getCampaignStatusHtml($campaign)
    {
        // To do refactor this and push the rendering logic to vue frontend
        if ($campaign->status === "active") {
            return '<span class="span_state status_success">Active</span>';
        } elseif ($campaign->status === "expired") {
            return '<span class="span_state status_danger">Finished</span>';
        } elseif ($campaign->status === "pending") {
            return '<span class="span_state status_pending">Pending</span>';
        } elseif ($campaign->status === "on_hold") {
            return '<span class="span_state status_on_hold">On Hold</span>';
        } else {
            return '<span class="span_state status_danger">File Error</span>';
        }
    }

    public function generateRedirectUrl($campaign)
    {
        if ($campaign->status === "pending" || $campaign->status === "on_hold") {
            return route('agency.campaign.details', ['id' => $campaign->id]);
        } else {
            return route('agency.campaign.all');
        }
    }
}