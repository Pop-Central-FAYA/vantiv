<?php

namespace Vanguard\Services\Brands;

use Vanguard\Libraries\Utilities;

class BrandCampaigns
{
    protected $brand_id;
    protected $client_id;
    protected $company_id;

    public function __construct($brand_id, $client_id, $company_id)
    {
        $this->brand_id = $brand_id;
        $this->client_id = $client_id;
        $this->company_id = $company_id;
    }

    public function baseQuery()
    {
        return Utilities::switch_db('api')->table('campaignDetails')
            ->where([
                ['campaignDetails.brand', $this->brand_id],
                ['campaignDetails.walkins_id', $this->client_id]
            ])
            ->whereIn('campaignDetails.launched_on', $this->company_id);
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
                        ->join('paymentDetails', function ($query) {
                            return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                                        ->on('paymentDetails.broadcaster', '=', 'campaignDetails.launched_on');
                        })
                        ->select('paymentDetails.amount AS total')
                        ->get()
                        ->sum('total', function ($variable) {
                            return $variable;
                        });
    }
}
