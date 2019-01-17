<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\InvoiceDetail;

class StoreInvoiceDetails
{
    protected $invoice_id;
    protected $invoice_number;
    protected $broadcaster_id;
    protected $agency_id;
    protected $user_id;
    protected $client_id;
    protected $total_spent;
    protected $preselected_adslot_group;

    public function __construct($invoice_id, $invoice_number, $broadcaster_id, $agency_id, $user_id, $client_id, $total_spent,$preselected_adslot_group)
    {
        $this->invoice_id = $invoice_id;
        $this->invoice_number = $invoice_number;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->user_id = $user_id;
        $this->client_id = $client_id;
        $this->total_spent = $total_spent;
        $this->preselected_adslot_group = $preselected_adslot_group;
    }

    public function storeInvoiceDetails()
    {
        $invoice_details = new InvoiceDetail();
        $invoice_details->id = uniqid();
        $invoice_details->invoice_id = $this->invoice_id;
        $invoice_details->user_id = $this->user_id;
        $invoice_details->invoice_number = $this->invoice_number;
        $invoice_details->actual_amount_paid = $this->agency_id ? $this->preselected_adslot_group->total : $this->total_spent;
        $invoice_details->refunded_amount = 0;
        $invoice_details->walkins_id = $this->client_id;
        $invoice_details->agency_id = $this->agency_id ? $this->agency_id : '';
        $invoice_details->agency_broadcaster = $this->agency_id ? $this->preselected_adslot_group->broadcaster_id : '';
        $invoice_details->broadcaster_id = $this->agency_id ? $this->preselected_adslot_group->broadcaster_id : $this->broadcaster_id;
        $invoice_details->save();
        return $invoice_details;

    }
}
