<?php

namespace Vanguard\Services\Traits;

trait CampaignQueryTrait
{
    public function campaignBaseQuery()
    {
        return \DB::table('campaignDetails')
                ->join('campaigns', 'campaigns.id', '=', 'campaignDetails.campaign_id')
                ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
                ->join('paymentDetails', function ($query){
                    return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                                    ->on('paymentDetails.broadcaster', '=', 'campaignDetails.launched_on');
                })
                ->join('brands', 'brands.id', '=', 'campaignDetails.brand')
                ->select('campaignDetails.adslots_id',
                    'campaignDetails.stop_date',
                    'campaignDetails.start_date',
                    'campaignDetails.status',
                    'campaignDetails.time_created',
                    'campaignDetails.product',
                    'campaignDetails.name',
                    'campaignDetails.campaign_id',
                    'campaignDetails.launched_on',
                    'payments.total',
                    'brands.name AS brand_name',
                    'campaigns.campaign_reference',
                    'payments.id AS payment_id',
                    'campaignDetails.min_age',
                    'campaignDetails.max_age',
                    'campaignDetails.agency',
                    'campaignDetails.Industry',
                    'campaignDetails.sub_industry',
                    'campaignDetails.broadcaster',
                    'campaignDetails.channel',
                    'campaignDetails.target_audience',
                    'campaignDetails.region',
                    'campaignDetails.user_id',
                    'campaignDetails.channel',
                    'paymentDetails.amount AS individual_broadcaster_sum',
                    'campaignDetails.adslots'
                );
    }
}
