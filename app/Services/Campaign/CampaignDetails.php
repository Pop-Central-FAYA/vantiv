<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\Campaign;
use Vanguard\Models\CampaignChannel;
use Vanguard\Models\TargetAudience;

class CampaignDetails
{
    protected $campaign_id;

    public function __construct($campaign_id)
    {
        $this->campaign_id = $campaign_id;
    }

    public function run()
    {
        $details = $this->getCampaignDetails();
        $details = $this->formatCampaignDetails($details);
        return $details;
    }

    public function getCampaignDetails()
    {
        $agency_id = \Auth::user()->companies->first()->id;
        return Campaign::with(['client', 'brand', 'campaign_mpos.campaign_mpo_time_belts'])
                        ->where('id', $this->campaign_id)
                        ->where('belongs_to', $agency_id)
                        ->first();
    }

    public function formatCampaignDetails($campaign)
    {
        $campaign->budget = number_format($campaign->budget,2);
        $campaign->channel_information = $this->getMediaChannels(json_decode($campaign->channel));
        $campaign->audience_information = $this->getTargetAudience(json_decode($campaign->target_audience));
        return $campaign;
    }

    public function getMediaChannels($channelIds)
    {
        return CampaignChannel::whereIn('id', $channelIds)->get();
    }

    public function getTargetAudience($audienceIds)
    {
        return TargetAudience::whereIn('id', $audienceIds)->get();
    }
}
