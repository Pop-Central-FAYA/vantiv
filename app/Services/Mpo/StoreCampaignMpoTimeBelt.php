<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\CampaignMpoTimeBelt;
use Vanguard\Services\BaseServiceInterface;

class StoreCampaignMpoTimeBelt extends UpdateTimeBeltService
{
    protected $request;
    protected $mpo_id;
    protected $id;

    public function __construct($request, $mpo_id)
    {
        $this->id = uniqid();
        parent::__construct(['id' => [$this->id]]);
        $this->request = $request;
        $this->mpo_id = $mpo_id;
    }

    public function run()
    {
        $time_belt = new CampaignMpoTimeBelt();
        $time_belt->id = $this->id;
        $time_belt->mpo_id = $this->mpo_id;
        $time_belt->time_belt_start_time = $this->request['time_belt'];
        $time_belt->time_belt_end_date = $this->request['time_belt'];
        $time_belt->day = date('l', strtotime($this->request['playout_date']));
        $time_belt->duration = $this->request['duration'];
        $time_belt->program = $this->request['program'];
        $time_belt->ad_slots = $this->request['insertion'];
        $time_belt->playout_date = $this->request['playout_date'];
        $time_belt->asset_id = $this->request['asset_id'];
        $time_belt->volume_discount = $this->request['volume_discount'];
        $time_belt->unit_rate = $this->request['unit_rate'];
        $time_belt->save();

        $this->updateNetTotal();
    }
}