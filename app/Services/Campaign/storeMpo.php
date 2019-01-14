<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\Mpo;

class storeMpo
{
    protected $mpo_id;
    protected $campaign_id;
    protected $invoice_number;
    protected $campaign_reference;

    public function __construct($mpo_id, $campaign_id, $invoice_number, $campaign_reference)
    {
        $this->mpo_id = $mpo_id;
        $this->campaign_id = $campaign_id;
        $this->invoice_number = $invoice_number;
        $this->campaign_reference = $campaign_reference;
    }

    public function storeMpo()
    {
        $mpo = new Mpo();
        $mpo->id = $this->mpo_id;
        $mpo->campaign_id = $this->campaign_id;
        $mpo->campaign_reference = $this->campaign_reference;
        $mpo->save();
        return $mpo;
    }
}
