<?php

namespace Vanguard\Services\Mpo;

class StoreCampaignMpoTimeBelt extends UpdateCampaignMpoTimeBelt
{
    protected $request;
    protected $time_belt;

    public function __construct($request, $time_belt)
    {
        $this->request = $request;
        $this->time_belt = $time_belt;
    }

    public function run()
    {
        $this->time_belt->mpo_id = $this->request->mpo_id;
        $this->time_belt->time_belt_start_time = $this->request->time_belt;
        $this->time_belt->time_belt_end_date = $this->request->time_belt;
        $this->time_belt->day = date('l', strtotime($this->request->playout_date));
        $this->time_belt->duration = $this->request->duration;
        $this->time_belt->program = $this->request->program;
        $this->time_belt->ad_slots = $this->request->insertion;
        $this->time_belt->playout_date = $this->request->playout_date;
        $this->time_belt->asset_id = $this->request->asset_id;
        $this->time_belt->volume_discount = $this->request->volume_discount;
        $this->time_belt->net_total = $this->calculateNetTotal();
        $this->time_belt->unit_rate = $this->request->unit_rate;
        $this->time_belt->save();
    }
}