<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\PaymentDetail;

class StorePaymentDetails
{
    protected $payment_id;
    protected $client_id;
    protected $now;
    protected $agency_id;
    protected $broadcaster_id;
    protected $campaign_budget;
    protected $total_spent;
    protected $preselected_group;

    public function __construct($payment_id, $broadcaster_id, $agency_id, $client_id, $campaign_budget, $total_spent, $now, $preselected_group)
    {
        $this->payment_id = $payment_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->client_id = $client_id;
        $this->campaign_budget = $campaign_budget;
        $this->total_spent = $total_spent;
        $this->now = $now;
        $this->preselected_group = $preselected_group;
    }

    public function storePaymentDetails()
    {
        $payment_details = new PaymentDetail();
        $payment_details->id = uniqid();
        $payment_details->payment_id = $this->payment_id;
        $payment_details->payment_method = '';
        $payment_details->amount = $this->agency_id ? (int)$this->preselected_group->total : (int)$this->total_spent;
        $payment_details->walkins_id = $this->client_id;
        $payment_details->time_created = date('Y-m-d H:i:s', $this->now);
        $payment_details->time_modified = date('Y-m-d H:i:s', $this->now);
        $payment_details->agency_id = $this->agency_id ? $this->agency_id : '';
        $payment_details->agency_broadcaster = $this->agency_id ? $this->preselected_group->broadcaster_id : '';
        $payment_details->broadcaster = $this->agency_id ? $this->preselected_group->broadcaster_id : $this->broadcaster_id;
        $payment_details->campaign_budget = $this->campaign_budget;
        $payment_details->save();
        return $payment_details;
    }
}
