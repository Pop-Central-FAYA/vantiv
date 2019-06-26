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
                $total_spots = $this->totalSpotsPerDuration($value->dates);

                $new_array[] = [
                    'station' => $suggestion->station,
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
                    'show_dates' => $value->dates
                ];
            }
        }

        return $new_array;
    }

    public function totalSpotsPerDuration($dates)
    {
        $spots = 0;
        foreach ($dates as $date => $num_spots) {
            $spots += $num_spots;
        }
        return $spots;
    }

    public function customMaterialDurations($material_lengths)
    {
        $new_array = [];
        if ($material_lengths) {
            foreach ($material_lengths as $key => $value) {
                $dates = [];
    
                foreach ($value as $details) {
                    if ($details->slot === '') {
                        $details->slot = 0;
                    }
                    $dates[$details->date] = $details->slot;
                }
    
                $new_array[$key]['unit_rate'] = $value[0]->unit_rate;
                $new_array[$key]['volume_disc'] = $value[0]->volume_disc;
                $new_array[$key]['day'] = $value[0]->day;
                $new_array[$key]['dates'] = $dates;
            }
        }
        return json_decode(json_encode($new_array));
    }
}