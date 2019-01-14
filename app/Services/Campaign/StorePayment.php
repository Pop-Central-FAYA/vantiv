<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\Payment;

class StorePayment
{
    protected $payment_id;
    protected $campaign_id;
    protected $total_spent;
    protected $now;
    protected $campaign_reference;
    protected $campaign_budget;

    public function __construct($payment_id, $campaign_id, $total_spent, $now, $campaign_reference, $campaign_budget)
    {
        $this->payment_id = $payment_id;
        $this->campaign_id = $campaign_id;
        $this->total_spent = $total_spent;
        $this->now = $now;
        $this->campaign_reference = $campaign_reference;
        $this->campaign_budget = $campaign_budget;
    }

    public function storePayment()
    {
        $payment = new Payment();
        $payment->id = $this->payment_id;
        $payment->campaign_id = $this->campaign_id;
        $payment->campaign_reference = $this->campaign_reference;
        $payment->total = $this->total_spent;
        $payment->time_created = date('Y-m-d H:i:s', $this->now);
        $payment->time_modified = date('Y-m-d H:i:s', $this->now);
        $payment->campaign_budget = $this->campaign_budget;
        $payment->save();
        return $payment;
    }
}
