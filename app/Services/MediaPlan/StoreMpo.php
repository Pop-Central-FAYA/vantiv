<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\CampaignTimeBelt;
use Vanguard\Models\TvStation;

use function GuzzleHttp\json_decode;

class StoreMpo
{
    protected $media_plan;
    protected $campaign_id;
    protected $suggestions;

    public function __construct($campaign_id, $media_plan, $suggestions)
    {
        $this->media_plan = $media_plan;    
        $this->campaign_id = $campaign_id;
        $this->suggestions = $suggestions;
    }

    public function run()
    {
        $tv_stations = TvStation::all();
        $stations = $this->suggestions->groupBy('station_type');
        foreach ($stations as $key => $time_belts) {
            foreach ($time_belts as $time_belt) {
                foreach ($time_belt['individual_details'] as $key => $individual_slot) {
                    $timebelt = CampaignTimeBelt::Create([
                        'mpo_id' => '',
                        'time_belt_start_time' => $time_belt['start_time'],
                        'time_belt_end_date' => $time_belt['end_time'],
                        'day' => $time_belt['day'],
                        'duration' => $time_belt['duration'],
                        'program' => $time_belt['program'],
                        'ad_slots' => $individual_slot->exposure,
                        'playout_date' => date('Y-m-d', strtotime($individual_slot->date)),
                        'volume_discount' => $individual_slot->vol_disc,
                        'net_total' => $individual_slot->net_total,
                        'unit_rate' => $individual_slot->unit_rate,
                        'campaign_id' => $this->campaign_id,
                        'publisher_id' => $this->getTvStationId($tv_stations, $time_belt['station'], $time_belt['type'])
                    ]);
                }
            }
        }
        
        return $stations;
    }

    private function getTvStationId($tv_stations, $station_name, $station_type)
    {
        return $tv_stations->where('name', $station_name)->where('type', $station_type)->pluck('publisher_id')[0];
    }
}