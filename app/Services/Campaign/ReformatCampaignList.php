<?php

namespace Vanguard\Services\Campaign;
use Vanguard\Services\BaseServiceInterface;

class ReformatCampaignList implements BaseServiceInterface
{
    protected $campaigns;
 

    public function __construct($campaigns)
    {
        $this->campaigns = $campaigns;
      
    }
    public function run()
    {
        $new_campaigns = [];
        foreach ($this->campaigns as $campaign) {
            $new_campaigns[] = [
                'id' => $campaign->campaign_reference,
                'campaign_id' => $campaign->id,
                'name' => $campaign->name,
                'product' => $campaign->product,
                'brand' => ucfirst($campaign->brand['name']),
                'date_created' => date('Y-m-d', strtotime($campaign->time_created)),
                'start_date' => date('Y-m-d', strtotime($campaign->start_date)),
                'end_date' => date('Y-m-d', strtotime($campaign->stop_date)),
                'adslots' => $campaign->ad_slots,
                'budget' => number_format($campaign->budget,2),
                'status' => $this->getCampaignStatusHtml($campaign),
                'redirect_url' => $this->generateRedirectUrl($campaign),
                'station' => ''
            ];
        }
        return $new_campaigns;
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
