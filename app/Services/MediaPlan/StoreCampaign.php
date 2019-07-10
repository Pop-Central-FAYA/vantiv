<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\Campaign;

class StoreCampaign
{
    protected $now;
    protected $channel;
    protected $target_audience;
    protected $created_by;
    protected $belongs_to;
    protected $other_information;
    protected $campaign_reference;
    protected $budget;
    protected $ad_slots;

    public function __construct($now, $campaign_reference, $channel, $target_audience, $created_by, $belongs_to, $other_information, $budget, $ad_slots)
    {
        $this->now = $now;
        $this->campaign_reference = $campaign_reference;
        $this->channel = $channel;
        $this->target_audience = $target_audience;
        $this->created_by = $created_by;
        $this->belongs_to = $belongs_to;
        $this->other_information = $other_information;
        $this->budget = $budget;
        $this->ad_slots = $ad_slots;
    }

    public function run()
    {
        $campaign = new Campaign();
        $campaign->time_created = date('Y-m-d H:i:s', $this->now);
        $campaign->time_modified = date('Y-m-d H:i:s', $this->now);
        $campaign->campaign_reference = $this->campaign_reference;
        $campaign->name = $this->other_information->campaign_name;
        $campaign->product = $this->other_information->product_name;
        $campaign->start_date = $this->other_information->start_date;
        $campaign->stop_date = $this->other_information->end_date;
        $campaign->walkin_id = $this->other_information->client_id;
        $campaign->brand_id = $this->other_information->brand_id;
        $campaign->age_groups = $this->other_information->criteria_age_groups;
        $campaign->regions = $this->other_information->criteria_region;
        $campaign->agency_commission = $this->other_information->agency_commission;
        $campaign->lsm = $this->other_information->criteria_lsm;
        $campaign->social_class = $this->other_information->criteria_social_class;
        $campaign->states = $this->other_information->criteria_state;
        $campaign->channel = $this->channel;
        $campaign->target_audience = $this->target_audience;
        $campaign->created_by = $this->created_by;
        $campaign->belongs_to = $this->belongs_to;
        $campaign->budget = $this->budget;
        $campaign->ad_slots = $this->ad_slots;
        $campaign->save();
        return $campaign;
    }
}
