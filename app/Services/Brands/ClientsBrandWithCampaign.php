<?php

namespace Vanguard\Services\Brands;

class ClientsBrandWithCampaign
{
    protected $client_id;
    protected $company_id;

    public function __construct($client_id, $company_id)
    {
        $this->client_id = $client_id;
        $this->company_id = $company_id;
    }

    public function getClientsBrandWithCampaigns()
    {
        return \DB::table('brand_client')
                    ->join('brands', 'brands.id', '=', 'brand_client.brand_id')
                    ->join('campaignDetails', 'campaignDetails.brand', '=', 'brand_client.brand_id')
                    ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
                    ->join('paymentDetails', function ($query) {
                        return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                                    ->on('paymentDetails.broadcaster', '=', 'campaignDetails.launched_on');
                    })
                    ->select('brands.id AS id',
                        'brands.name AS brand',
                        'brands.created_at AS date',
                        'brands.image_url AS image_url',
                        'brands.industry_code AS industry_id',
                        'brands.sub_industry_code AS sub_industry_id',
                        'brand_client.media_buyer_id AS agency_broadcaster',
                        'brand_client.client_id AS client_walkins_id',
                        'campaignDetails.name AS last_campaign'
                    )
                    ->selectRaw("SUM(paymentDetails.amount) AS total,
                    COUNT(campaignDetails.campaign_id) AS campaigns
                    ")
                    ->where('brand_client.client_id', $this->client_id)
                    ->whereIn('campaignDetails.launched_on', $this->company_id)
                    ->groupBy('campaignDetails.brand')
                    ->get();
    }
}
