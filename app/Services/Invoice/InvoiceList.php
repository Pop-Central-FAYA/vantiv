<?php

namespace Vanguard\Services\Invoice;

use Vanguard\Services\Traits\InvoiceQueryTrait;

class InvoiceList
{
    protected $company_id;
    use InvoiceQueryTrait;
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function invoiceListQuery()
    {
        return $this->invoiceBaseQuery()
                    ->groupBy('invoiceDetails.invoice_id')
                    ->orderBy('invoiceDetails.time_created', 'DESC')
                    ->get();
    }

    public function pendingInvoiceListQuery()
    {
        return $this->invoiceBaseQuery()
                    ->where('invoiceDetails.status', 0)
                    ->groupBy('invoiceDetails.invoice_id')
                    ->orderBy('invoiceDetails.time_created', 'DESC')
                    ->get();
    }

}
