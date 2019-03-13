<?php

namespace Vanguard\Services\Traits;

trait InvoiceQueryTrait
{
    public function invoiceBaseQuery()
    {
        return \DB::table('invoiceDetails')
            ->join('invoices', 'invoices.id', '=', 'invoiceDetails.invoice_id')
            ->join('walkIns', 'walkIns.id', '=', 'invoiceDetails.walkins_id')
            ->join('campaignDetails', function ($query) {
                $query->on('campaignDetails.campaign_id', '=', 'invoices.campaign_id')
                      ->on('campaignDetails.launched_on', '=', 'invoiceDetails.broadcaster_id');
            })
            ->join('brands', 'brands.id', '=', 'campaignDetails.brand')
            ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
            ->select('invoiceDetails.invoice_id', 'invoiceDetails.invoice_number', 'payments.total AS actual_amount_paid',
                'invoiceDetails.refunded_amount', 'walkIns.company_name', 'invoiceDetails.status', 'brands.name',
                'campaignDetails.name AS campaign_name')
            ->selectRaw("DATE_FORMAT(invoiceDetails.time_created, '%Y-%m-%d') AS date");
    }
}
