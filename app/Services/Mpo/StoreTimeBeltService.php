<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\CampaignTimeBelt;

class StoreTimeBeltService extends UpdateTimeBeltService
{
    protected $validated_data;
    protected $campaign_id;
    public $id;

    public function __construct($validated_data, $campaign_id)
    {
        $this->id = uniqid();
        parent::__construct(['id' => [$this->id]]);
        $this->validated_data = $validated_data;
        $this->campaign_id = $campaign_id;
    }

    public function run()
    {
        $time_belt = new CampaignTimeBelt();
        $time_belt->id = $this->id;
        $time_belt->campaign_id = $this->campaign_id;
        $time_belt->time_belt_start_time = $this->validated_data['time_belt'];
        $time_belt->time_belt_end_date = $this->validated_data['time_belt'];
        $time_belt->day = date('l', strtotime($this->validated_data['playout_date']));
        $time_belt->duration = $this->validated_data['duration'];
        $time_belt->program = $this->validated_data['program'];
        $time_belt->ad_slots = $this->validated_data['insertion'];
        $time_belt->playout_date = $this->validated_data['playout_date'];
        $time_belt->asset_id = $this->validated_data['asset_id'];
        $time_belt->volume_discount = $this->validated_data['volume_discount'];
        $time_belt->unit_rate = $this->validated_data['unit_rate'];
        $time_belt->publisher_id = $this->validated_data['publisher'];
        $time_belt->ad_vendor_id = $this->validated_data['ad_vendor'];
        $time_belt->save();

        $this->updateNetTotal();
    }
}