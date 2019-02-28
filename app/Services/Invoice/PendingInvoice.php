<?php

namespace Vanguard\Services\Invoice;

class PendingInvoice
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getPendingInvoice()
    {
        return \DB::table('invoiceDetails')
                ->where('status', 0)
                ->when(!is_array($this->company_id), function($query) {
                    return $query->where('broadcaster_id', $this->company_id);
                })
                ->when(is_array($this->company_id), function($query) {
                    return $query->whereIn('broadcaster_id', $this->company_id);
                })
                ->get();
    }
}
