<?php

namespace Vanguard\Services\Traits;

trait MpoQueryTrait
{
    public function mpoBaseQuery()
    {
        return \DB::table('mpoDetails')
            ->join('mpos', 'mpos.id', '=', 'mpoDetails.mpo_id')
            ->join('campaignDetails', function ($query) {
                return $query->on('campaignDetails.campaign_id', '=','mpos.campaign_id')
                    ->on('campaignDetails.launched_on', '=', 'mpoDetails.broadcaster_id');
            })
            ->join('brands', 'brands.id', '=', 'campaignDetails.brand')
            ->join('payments', 'payments.campaign_id', '=', 'mpos.campaign_id')
            ->join('paymentDetails', function($query) {
                return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                    ->on('paymentDetails.broadcaster', '=', 'mpoDetails.broadcaster_id');
            })
            ->join('invoices', 'invoices.campaign_id', '=', 'mpos.campaign_id')
            ->select('mpoDetails.mpo_id', 'mpoDetails.is_mpo_accepted', 'campaignDetails.status AS campaign_status',
                'mpoDetails.agency_id', 'mpos.campaign_id', 'campaignDetails.name', 'campaignDetails.product',
                'brands.name AS brand_name', 'invoices.invoice_number', 'campaignDetails.time_created AS date',
                'paymentDetails.amount AS amount', 'campaignDetails.brand', 'mpoDetails.broadcaster_id AS company_id');
    }
}
