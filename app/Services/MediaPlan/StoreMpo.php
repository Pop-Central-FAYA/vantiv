<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\CampaignMpo;
use Vanguard\Models\CampaignMpoTimeBelt;
use Yajra\DataTables\DataTables;
use Auth; 
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use DateTime;

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
        $stations = $this->suggestions->groupBy('station');
        foreach ($stations as $key => $time_belts) {
            $mpo = new CampaignMpo();
            $mpo->campaign_id = $this->campaign_id;
            $mpo->station = $key;
            $mpo->ad_slots = $time_belts->sum('total_spots');
            $mpo->budget = $time_belts->sum('net_value');
            $mpo->status = 'Pending';
            $mpo->save();
            foreach ($time_belts as $time_belt) {
                foreach ($time_belt['show_dates'] as $show_date => $ad_slots) {
                    $mpo_timebelt = CampaignMpoTimeBelt::Create([
                        'mpo_id' => $mpo->id,
                        'time_belt_start_time' => $time_belt['start_time'],
                        'time_belt_end_date' => $time_belt['end_time'],
                        'day' => $time_belt['day'],
                        'duration' => $time_belt['duration'],
                        'program' => $time_belt['program'],
                        'ad_slots' => $time_belt['total_spots'],
                        'playout_date' => date('Y-m-d', strtotime($show_date))
                    ]);
                }
            }
        }
        return $stations;
    }
}