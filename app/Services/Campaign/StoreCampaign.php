<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\Campaign;

class StoreCampaign
{
    protected $campaign_id;
    protected $now;
    protected $campaign_reference;

    public function __construct($campaign_id, $now, $campaign_reference)
    {
        $this->campaign_id = $campaign_id;
        $this->now = $now;
        $this->campaign_reference = $campaign_reference;
    }

    public function storeCampaign()
    {
        $campaign = new Campaign();
        $campaign->id = $this->campaign_id;
        $campaign->time_created = date('Y-m-d H:i:s', $this->now);
        $campaign->time_modified = date('Y-m-d H:i:s', $this->now);
        $campaign->campaign_reference = $this->campaign_reference;
        $campaign->save();
        return $campaign;
    }

    public function storeCampaingDetails()
    {
        $campaign_details = new CampaignDetail();
        $campaign_details->id = uniqid();
        $campaign_details->campaign_id = $this->campaign_id;
        $campaign_details->user_id = $this->user_id;
        $campaign_details->channel = $this->agency_id ? "'". implode("','" ,$this->campaign_general_information->channel) . "'" : "'". $this->broadcaster_details->channel_id . "'";
        $campaign_details->brand = $this->campaign_general_information->brand;
        $campaign_details->start_date = date('Y-m-d', strtotime($this->campaign_general_information->start_date));
        $campaign_details->stop_date = date('Y-m-d', strtotime($this->campaign_general_information->end_date));
        $campaign_details->name = $this->campaign_general_information->name;
        $campaign_details->product = $this->campaign_general_information->product;
        $campaign_details->day_parts = "'". implode("','" ,$this->campaign_general_information->dayparts) . "'";
        $campaign_details->target_audience =  "'". implode("','" ,$this->campaign_general_information->target_audience) . "'";
        $campaign_details->region =  "'". implode("','" ,$this->campaign_general_information->region) . "'";
        $campaign_details->min_age = (int)$this->campaign_general_information->min_age;
        $campaign_details->max_age = (int)$this->campaign_general_information->max_age;
        $campaign_details->industry = $this->campaign_general_information->industry;
        $campaign_details->adslots = $this->count_adslot;
        $campaign_details->walkins_id = $this->clients_id->id;
        $campaign_details->time_created = date('Y-m-d H:i:s', $this->now);
        $campaign_details->time_modified = date('Y-m-d H:i:s', $this->now);
        $campaign_details->adslots_id = $this->adslot_ids;
        $campaign_details->agency = $this->agency_id ? $this->agency_id : '';
        $campaign_details->agency_broadcaster = '';
        $campaign_details->broadcaster = $this->broadcaster_id;
        $campaign_details->sub_industry = $this->campaign_general_information->sub_industry;
        $campaign_details->status = CampaignStatus::ON_HOLD;
    }

    public function storeSelectedAdslot()
    {
        $selected_adslot = new SelectedAdslot();
        $selected_adslot->id = uniqid();
    }



}
