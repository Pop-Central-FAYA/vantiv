<?php

namespace Vanguard\Services\Brands;

use Vanguard\Libraries\Utilities;

class BrandCampaigns
{
    protected $brand_id;
    protected $client_id;
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($brand_id, $client_id, $broadcaster_id, $agency_id)
    {
        $this->brand_id = $brand_id;
        $this->client_id = $client_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
    }

    public function baseQuery()
    {
        return Utilities::switch_db('api')->table('campaignDetails')
            ->where([
                ['campaignDetails.brand', $this->brand_id],
                ['campaignDetails.walkins_id', $this->client_id]
            ])
            ->when($this->broadcaster_id, function($query) {
                return $query->where('campaignDetails.broadcaster', $this->broadcaster_id);
            });
    }

    public function getBrandCampaigns()
    {
        return $this->baseQuery()->get();
    }

    public function getBrandLastCampaign()
    {
        return $this->getBrandCampaigns()->last();
    }

    public function countAllBrandCampaigns()
    {
        return count($this->getBrandCampaigns());
    }

    public function getBrandTotalSpent()
    {
        return $this->baseQuery()
                        ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
                        ->sum('total');
    }
}
