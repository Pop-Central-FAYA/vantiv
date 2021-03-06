<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Models\CampaignDetail;

class StoreCampaignDetails
{
    protected $campaign_id;
    protected $user_id;
    protected $broadcaster_id;
    protected $agency_id;
    protected $campaign_general_information;
    protected $client_id;
    protected $broadcaster_channel;
    protected $now;
    protected $adslots_ids;
    protected $preselected_adslot_group;

    public function __construct($campaign_id, $user_id, $broadcaster_id, $agency_id, $campaign_general_information, $client_id, $broadcaster_channel, $now, $adslots_ids, $preselected_adslot_group)
    {
        $this->campaign_id = $campaign_id;
        $this->user_id = $user_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->campaign_general_information = $campaign_general_information;
        $this->client_id = $client_id;
        $this->broadcaster_channel = $broadcaster_channel;
        $this->now = $now;
        $this->adslots_ids = $adslots_ids;
        $this->preselected_adslot_group = $preselected_adslot_group;
    }

    public function storeCampaingDetails()
    {
        $campaign_details = new CampaignDetail();
        $campaign_details->id = uniqid();
        $campaign_details->campaign_id = $this->campaign_id;
        $campaign_details->user_id = $this->user_id;
        $campaign_details->channel = $this->agency_id ? "'". implode("','" ,$this->campaign_general_information->channel) . "'" : "'". $this->broadcaster_channel . "'";
        $campaign_details->brand = $this->campaign_general_information->brand;
        $campaign_details->start_date = date('Y-m-d', strtotime($this->campaign_general_information->start_date));
        $campaign_details->stop_date = date('Y-m-d', strtotime($this->campaign_general_information->end_date));
        $campaign_details->name = $this->campaign_general_information->campaign_name;
        $campaign_details->product = $this->campaign_general_information->product;
        $campaign_details->day_parts = "'". implode("','" ,$this->campaign_general_information->dayparts) . "'";
        $campaign_details->target_audience =  "'". implode("','" ,$this->campaign_general_information->target_audience) . "'";
        $campaign_details->region =  "'". implode("','" ,$this->campaign_general_information->region) . "'";
        $campaign_details->min_age = (int)$this->campaign_general_information->min_age;
        $campaign_details->max_age = (int)$this->campaign_general_information->max_age;
        $campaign_details->industry = $this->campaign_general_information->industry;
        $campaign_details->adslots = $this->agency_id ? $this->preselected_adslot_group->total_slot : count($this->adslots_ids);
        $campaign_details->walkins_id = $this->client_id;
        $campaign_details->time_created = date('Y-m-d H:i:s', $this->now);
        $campaign_details->time_modified = date('Y-m-d H:i:s', $this->now);
        $campaign_details->adslots_id = "'". implode("','" ,$this->adslots_ids) . "'";
        $campaign_details->agency = $this->agency_id ? $this->agency_id : '';
        $campaign_details->agency_broadcaster = $this->agency_id ? $this->preselected_adslot_group->broadcaster_id : '';
        $campaign_details->broadcaster = $this->agency_id ? $this->preselected_adslot_group->broadcaster_id : $this->broadcaster_id;
        $campaign_details->sub_industry = $this->campaign_general_information->sub_industry;
        $campaign_details->status = CampaignStatus::ON_HOLD;
        $campaign_details->created_by = \Auth::user()->id;
        $campaign_details->belongs_to = $this->agency_id ? $this->agency_id : $this->broadcaster_id;
        $campaign_details->launched_on = $this->agency_id ? $this->preselected_adslot_group->broadcaster_id : $this->broadcaster_id;
        $campaign_details->save();
        return $campaign_details;
    }
}
