<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\CampaignMpoTimeBelt;

class UpdateCampaignMpoTimeBelt
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function run()
    {
        $mpo_time_belt = CampaignMpoTimeBelt::find($this->request->id);
        $mpo_time_belt->program = $this->request->program;
        $mpo_time_belt->playout_date = $this->request->playout_date;
        $mpo_time_belt->day = date('l', strtotime($this->request->playout_date));
        $mpo_time_belt->asset_id = $this->request->asset_id;
        $mpo_time_belt->unit_rate = $this->request->unit_rate;
        $mpo_time_belt->volume_discount = $this->request->volume_discount;
        $mpo_time_belt->ad_slots = $this->request->insertion;
        $mpo_time_belt->time_belt_start_time = $this->request->time_belt;
        $mpo_time_belt->net_total = $this->calculateNetTotal();
        $mpo_time_belt->save();
        return $mpo_time_belt;
    }

    public function calculateNetTotal()
    {
        $gross_value = $this->request->insertion * $this->request->unit_rate;
        $discount_value = ($this->request->discount / 100) * $gross_value;
        return $gross_value - $discount_value;
    }
}