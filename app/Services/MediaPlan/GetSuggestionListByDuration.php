<?php

namespace Vanguard\Services\MediaPlan;
use Illuminate\Support\Collection;

class GetSuggestionListByDuration
{
    protected $media_plan;

    public function __construct($media_plan)
    {
        $this->media_plan = $media_plan;    
    }

    public function run()
    {
        $suggestions = $this->refinedMediaPlanSuggestions();
        return collect($suggestions);
    }

    public function getTotalAdSlotPerPlan($suggestions)
    {
        $suggestions = collect($suggestions);
        return $suggestions->sum('total_spots');
    }

    public function getTotalBudgetPerPlan($suggestions)
    {
        $suggestions = collect($suggestions);
        return $suggestions->sum('net_value');
    }

    public function refinedMediaPlanSuggestions()
    {
        $selected_suggestions = $this->media_plan->suggestions
                                    ->where('material_length', '!=', null)
                                    ->where('status', 1);

        $suggestions = $selected_suggestions->map(function($item, $key) {
                        $item->material_length = $this->customMaterialDurations(json_decode($item->material_length));
                        return $item;
                    });

        $agency_commission = $this->media_plan->agency_commission;
        $plan_start_date = $this->media_plan->start_date;
        $plan_end_date = $this->media_plan->end_date;

        $new_array = [];

        foreach ($suggestions as $suggestion) {
            foreach ($suggestion->material_length as $key => $value) {
                $gross_unit_rate = (int) $value->unit_rate;
                $volume_discount = (int) $value->volume_disc;
                $value_less = $gross_unit_rate * ((100 - $volume_discount) / 100);
                $net_unit_rate = $value_less * ((100 - $agency_commission) / 100);
                $total_spots = $this->totalSpotsPerDuration($value->slot_details);

                $new_array[] = [
                    'station' => $suggestion->station,
                    'type' => $suggestion->station_type,
                    'station_type' => $suggestion->station.'_'.$suggestion->station_type,
                    'program' => $suggestion->program,
                    'duration' => $key,
                    'day' => $value->day,
                    'start_time' => $suggestion->start_time,
                    'end_time' => $suggestion->end_time,
                    'gross_unit_rate' => $gross_unit_rate,
                    'volume_discount' => $volume_discount,
                    'agency_commission' => $agency_commission,
                    'value_less' => $value_less,
                    'net_unit_rate' => $net_unit_rate,
                    'total_spots' => $total_spots,
                    'gross_value' => $gross_unit_rate * $total_spots,
                    'net_value' => $net_unit_rate * $total_spots,
                    'net_value_after_bonus_spots' => $net_unit_rate * $total_spots,
                    'individual_details' => $value->slot_details,
                ];
            }
        }
        return $new_array;
    }

    public function totalSpotsPerDuration($slot_details)
    {
        $spots = 0;
        foreach ($slot_details as $key => $slot) {
            $spots += $slot->exposure;
        }
        return $spots;
    }

    public function customMaterialDurations($material_lengths)
    {
        $new_array = [];
        if ($material_lengths) {
            foreach ($material_lengths as $key => $value) {
                $individual_spot_details = [];
    
                foreach ($value as $details) {
                    if ($details->slot === '') {
                        $details->slot = 0;
                    }
                    $individual_spot_details[] = [
                        'date' => $details->date,
                        'vol_disc' => $details->volume_disc,
                        'net_total' => $details->net_total,
                        'unit_rate' => $details->unit_rate,
                        'exposure' => $details->slot
                    ];
                }
    
                $new_array[$key]['unit_rate'] = $value[0]->unit_rate;
                $new_array[$key]['volume_disc'] = $value[0]->volume_disc;
                $new_array[$key]['day'] = $value[0]->day;
                $new_array[$key]['slot_details'] = $individual_spot_details;
            }
        }
        return json_decode(json_encode($new_array));
    }
}