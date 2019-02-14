<?php

namespace Vanguard\Services\Client;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Utilities;

class ClientCampaigns
{
    protected $client_id;
    protected $company_id;

    public function __construct($client_id, $company_id)
    {
        $this->company_id = $company_id;
        $this->client_id = $client_id;
    }

    public function baseQuery()
    {
        return \DB::table('campaignDetails')
            ->when(!is_array($this->company_id), function($query) {
                return $query->where('campaignDetails.launched_on', $this->company_id);
            })
            ->when(is_array($this->company_id), function ($query) {
                return $query->whereIn('campaignDetails.launched_on', $this->company_id);
            })
            ->where('campaignDetails.walkins_id', $this->client_id);
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
            ->join('companies', 'companies.id', '=', 'campaignDetails.launched_on')
            ->join('paymentDetails', function ($query) {
                return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                            ->on('paymentDetails.broadcaster', '=', 'campaignDetails.launched_on');
            })
            ->select('campaignDetails.campaign_id', 'campaignDetails.name', 'campaignDetails.product',
                'campaignDetails.start_date', 'campaignDetails.stop_date', 'campaignDetails.adslots', 'campaignDetails.adslots_id',
                'campaigns.campaign_reference','payments.total', 'brands.name AS brands', 'campaignDetails.time_created',
                'brands.name AS brand', 'payments.campaign_budget AS budget', 'campaignDetails.status',
                'paymentDetails.amount AS individual_publisher_total'
                )
            ->where('campaignDetails.adslots', '>', 0)
            ->orderBy('campaignDetails.time_created', 'DESC')
            ->when(is_array($this->company_id), function ($query) {
                return $query->groupBy('campaignDetails.campaign_id');
            })
            ->get();

    }

    public function getClientTotalSpent()
    {
        return $this->baseQuery()
            ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
            ->join('paymentDetails', function ($query) {
                return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                    ->on('paymentDetails.broadcaster', '=', 'campaignDetails.launched_on');
            })
            ->sum('paymentDetails.amount');
    }

    public function getPublishers()
    {
        return $this->baseQuery()
                    ->join('walkIns', 'walkIns.id', '=', 'campaignDetails.walkins_id')
                    ->join('companies', 'companies.id', '=', 'campaignDetails.launched_on')
                    ->selectRaw("GROUP_CONCAT(DISTINCT companies.name) AS company_name, 
                                            GROUP_CONCAT(DISTINCT companies.logo) AS company_logo,
                                            GROUP_CONCAT(DISTINCT companies.id) AS company_id, 
                                            count(DISTINCT campaignDetails.campaign_id) AS campaign_count"
                    )
                    ->where('campaignDetails.walkins_id', $this->client_id)
                    ->whereIn('campaignDetails.launched_on', $this->company_id)
                    ->groupBy('campaignDetails.walkins_id')
                    ->get();

    }

}
