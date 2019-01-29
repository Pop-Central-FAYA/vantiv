<?php

namespace Vanguard\Services\Client;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Utilities;

class ClientCampaigns
{
    protected $user_id;
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($user_id, $broadcaster_id, $agency_id)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->user_id = $user_id;
    }

    public function baseQuery()
    {
        return Utilities::switch_db('api')->table('campaignDetails')
            ->when($this->broadcaster_id, function($query) {
                return $query->where('campaignDetails.broadcaster', $this->broadcaster_id);
            })
            ->where('campaignDetails.user_id', $this->user_id);
    }

    public function clientCampaigns()
    {
        return $this->baseQuery()->get();
    }

    public function countActiveCampaigns()
    {
        return $this->baseQuery()->where('status', CampaignStatus::ACTIVE_CAMPAIGN)
                                ->count();
    }

    public function countInactiveCampaigns()
    {
        return $this->baseQuery()->where('status', '!=', CampaignStatus::ACTIVE_CAMPAIGN)
                                ->count();
    }

    public function getLastCampaign()
    {
        return $this->clientCampaigns()->last();
    }

    public function countAllClientCampaigns()
    {
        return count($this->clientCampaigns());
    }

    public function getComprehensiveDetails()
    {
        return $this->baseQuery()
            ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
            ->join('brands', 'brands.id', '=', 'campaignDetails.brand')
            ->join('campaigns', 'campaigns.id', '=', 'campaignDetails.campaign_id')
            ->select('campaignDetails.campaign_id', 'campaignDetails.name', 'campaignDetails.product',
                'campaignDetails.start_date', 'campaignDetails.stop_date', 'campaignDetails.adslots',
                'campaigns.campaign_reference','payments.total', 'brands.name AS brands', 'campaignDetails.time_created',
                'brands.name AS brand', 'payments.campaign_budget AS budget', 'campaignDetails.status'
                )
            ->where('campaignDetails.adslots', '>', 0)
            ->orderBy('campaignDetails.time_created', 'DESC')
            ->get();

    }

    public function getClientTotalSpent()
    {
        return $this->baseQuery()
            ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
            ->sum('total');
    }

}
