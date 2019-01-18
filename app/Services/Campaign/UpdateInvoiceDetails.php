<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Utilities;

class UpdateInvoiceDetails
{
    protected $invoice_id;

    public function __construct($invoice_id)
    {
        $this->invoice_id = $invoice_id;
    }

    public function updateInvoiceDetails()
    {
        return Utilities::switch_db('api')->table('invoiceDetails')
                            ->where('id', $this->invoice_id)
                            ->update(['status' => 1]);
    }
}
