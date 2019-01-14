<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\Invoice;

class StoreInvoice
{
    protected $invoice_id;
    protected $campaign_id;
    protected $campaign_reference;
    protected $payment_id;
    protected $invoice_number;

    public function __construct($invoice_id, $campaign_id, $campaign_reference, $payment_id, $invoice_number)
    {
        $this->invoice_id = $invoice_id;
        $this->campaign_id = $campaign_id;
        $this->campaign_reference = $campaign_reference;
        $this->payment_id = $payment_id;
        $this->invoice_number = $invoice_number;
    }

    public function storeInvoice()
    {
        $invoice = new Invoice();
        $invoice->id = $this->invoice_id;
        $invoice->campaign_id = $this->campaign_id;
        $invoice->campaign_reference = $this->campaign_reference;
        $invoice->invoice_number = $this->invoice_number;
        $invoice->payment_id = $this->payment_id;
    }
}
