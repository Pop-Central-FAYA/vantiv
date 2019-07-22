<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\CampaignMpo;
use Vanguard\Models\CampaignMpoTimeBelt;
use Yajra\DataTables\DataTables;
use Auth; 
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use DateTime;
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
        $stations = $this->suggestions->groupBy('station');
        foreach ($stations as $key => $time_belts) {
            $mpo = new CampaignMpo();
            $mpo->campaign_id = $this->campaign_id;
            $mpo->station = $key;
            $mpo->ad_slots = $this->countTotalSlots($time_belts);
            $mpo->budget = $this->sumBudget($time_belts);
            $mpo->status = 'Pending';
            $mpo->save();
            foreach ($time_belts as $time_belt) {
                foreach ($time_belt['individual_details'] as $key => $individual_slot) {
                    $mpo_timebelt = CampaignMpoTimeBelt::Create([
                        'mpo_id' => $mpo->id,
                        'time_belt_start_time' => $time_belt['start_time'],
                        'time_belt_end_date' => $time_belt['end_time'],
                        'day' => $time_belt['day'],
                        'duration' => $time_belt['duration'],
                        'program' => $time_belt['program'],
                        'ad_slots' => $individual_slot->exposure,
                        'playout_date' => date('Y-m-d', strtotime($individual_slot->date)),
                        'volume_discount' => $individual_slot->vol_disc,
                        'net_total' => $individual_slot->net_total,
                        'unit_rate' => $individual_slot->unit_rate
                    ]);
                }
            }
        }
        return $stations;
    }

    public function countTotalSlots($timeBeltArr)
    {
        $total_slots = 0;
        foreach ($timeBeltArr as $time_belt) {
            foreach ($time_belt['individual_details'] as $key => $individual_slot) {
                $total_slots += count(json_decode(json_encode($individual_slot), true));
            }
        }
        return $total_slots;
    }

    public function sumBudget($timeBeltArr)
    {
        $summed_budget = 0;
        foreach ($timeBeltArr as $time_belt) {
            foreach ($time_belt['individual_details'] as $key => $individual_slot) {
                $summed_budget += (INT) $individual_slot->net_total;
            }
        }
        return $summed_budget;
    }
}