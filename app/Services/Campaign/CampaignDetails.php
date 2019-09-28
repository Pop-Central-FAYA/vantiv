<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\Campaign;
use Vanguard\Models\CampaignChannel;
use Vanguard\Models\TargetAudience;

class CampaignDetails
{
    protected $campaign_id;
    protected $group_parameter;

    public function __construct($campaign_id, $group_parameter = null)
    {
        $this->campaign_id = $campaign_id;
        $this->group_parameter = $group_parameter === 'ad_vendor_id' ? 'ad_vendor_id' : 'publisher_id';
    }

    public function run()
    {
        $details = $this->getCampaignDetails();
        $formatted_details = $this->formatCampaignDetails($details);
        return $formatted_details;
    }

    public function getCampaignDetails()
    {
        $agency_id = \Auth::user()->companies->first()->id;
        return Campaign::with(['creator', 'client', 'brand', 'time_belts.publisher', 'time_belts.vendor', 'time_belts.media_asset',
                            'time_belts.publisher.ad_vendors'])
                        ->where('id', $this->campaign_id)
                        ->where('belongs_to', $agency_id)
                        ->first();
    }

    public function formatCampaignDetails($campaign)
    {
        $campaign->budget = $campaign->budget;
        $channel_arr = $this->getMediaChannels(json_decode($campaign->channel));
        $campaign->media_type = implode(', ', $channel_arr);
        $campaign->flight_date = date('M d, Y', strtotime($campaign->start_date)).' to '.date('M d, Y', strtotime($campaign->stop_date));
        $campaign->created_at = date('M d, Y', strtotime($campaign->time_created));
        $audience_arr = $this->getTargetAudience(json_decode($campaign->target_audience));
        $campaign->gender = implode(', ', $audience_arr);
        $campaign->status = ucfirst($campaign->status);

        if (is_array(json_decode($campaign->lsm))) {
            $campaign->lsm = implode(', ', json_decode($campaign->lsm));
        }
        if (is_array(json_decode($campaign->social_class))) {
            $campaign->social_class = implode(', ', json_decode($campaign->social_class));
        }
        if (is_array(json_decode($campaign->states))) {
            $campaign->states = implode(', ', json_decode($campaign->states));
        }
        if (is_array(json_decode($campaign->regions))) {
            $campaign->regions = implode(', ', json_decode($campaign->regions));
        }
        if (is_array(json_decode($campaign->age_groups))) {
            $age_groups_str = '';
            foreach(json_decode($campaign->age_groups) as $key=>$age_group) {
                $age_groups_str .= $age_group->min.' - '.$age_group->max;
                if (($key+1) < count(json_decode($campaign->age_groups))) {
                    $age_groups_str .= ', ';
                }
            }
            $campaign->age_groups = $age_groups_str;
        }
        $campaign['grouped_time_belts'] = $this->groupTimeBelts($campaign);
        
        return $campaign;
    }

    public function getMediaChannels($channelIds)
    {
        return CampaignChannel::whereIn('id', $channelIds)->get()->pluck('channel')->toArray();
    }

    public function getTargetAudience($audienceIds)
    {
        return TargetAudience::whereIn('id', $audienceIds)->get()->pluck('audience')->toArray();
    }

    public function groupTimeBelts($campaign)
    {
        $format_grouped_time_belts = [];
        $grouped_time_belts = $campaign->time_belts->groupBy($this->group_parameter);
        foreach($grouped_time_belts as $key => $group_time_belt){
            $format_grouped_time_belts[] = [
                $this->group_parameter => $key,
                'insertions' => $group_time_belt->sum('ad_slots'),
                'net_total' => $group_time_belt->sum('net_total'),
                'time_belts' => $group_time_belt
            ];
        }
        return $format_grouped_time_belts;
    }
}
