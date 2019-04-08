<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Yajra\DataTables\DataTables;
use Auth;

class SummarizePlan
{
    protected $media_plan;

    public function __construct($media_plan)
    {
        $this->media_plan = $media_plan;    
    }

    public function run()
    {
        return $this->summarizeMediaPlan($this->media_plan);
    }

    public function summarizeMediaPlan($mediaPlan)
    {
        $selectedSuggestions = $mediaPlan->suggestions->where('status', 1);
        $summaryData = $selectedSuggestions->map(function($item, $key) use ($mediaPlan) {
                        $material_lengths = $this->customMaterialDurations(json_decode($item->material_length));
                        $item->num_spots = $this->numSpotsPerStationTimebelt($material_lengths);
                        $volume_discount = 0;
                        $agency_commission = $mediaPlan->agency_commission;

                        $cost_summary = json_decode($this->costSummaryPerTimeBelt($material_lengths, $volume_discount, $agency_commission));

                        $item->gross_value = $cost_summary->gross_value;
                        $item->net_value = $cost_summary->net_value;
                        $item->savings = $cost_summary->savings;
                        $item->material_durations = $cost_summary->durations;


                        // $item->volume_discount = 0;
                        // $item->agency_commission = $mediaPlan->agency_commission;
                        // $item->gross_unit_rate = $this->grossUnitRatePerTimebelt($material_lengths, $item->num_spots);
                        // $item->value_less = $item->gross_unit_rate * ((100 - $item->volume_discount) / 100);
                        // $item->net_unit_rate = $item->value_less * ((100 - $item->agency_commission) / 100);
                        // $item->bonus_spots = 0;
                        // $item->cost_bonus_spots = 0;
                        // $item->gross_value = 
                        // $item->net_value = 
                        // $item->net_value_after_bonus_spots = 

                        return $item;
                    });

        return json_decode($this->summaryGroupByMedium($summaryData));
    }

    public function customMaterialDurations($material_lengths)
    {
        $new_array = [];

        foreach ($material_lengths as $key => $value) {
            $dates = [];

            foreach ($value as $details) {
                $dates[$details->date] = $details->slot;
            }

            $new_array[$key]['unit_rate'] = $value[0]->unit_rate;
            $new_array[$key]['volume_disc'] = $value[0]->volume_disc;
            $new_array[$key]['days'] = $dates;
        }
        return json_decode(json_encode($new_array));
    }

    public function summaryGroupByMedium($summary)
    {
        $summaryByMedium = $summary->groupBy('media_type');

        $groupByResult = [];

        foreach ($summaryByMedium as $key => $value) {

            $groupByResult[] = [
                'medium' => $key,
                'gross_value' => $value->sum('gross_value'),
                'net_value' => $value->sum('net_value'),
                'savings' => $value->sum('savings'),
                'total_spots' => $value->sum('num_spots'),
                'material_durations' => $value->pluck('material_durations')->unique()->collapse()
            ];
        }

        return json_encode($groupByResult);
    }

    public function numSpotsPerStationTimebelt($material_lengths)
    {
        $spots = 0;
        foreach ($material_lengths as $key => $value) {
            foreach ($value->days as $date => $num_spots) {
                $spots += $num_spots;
            }
        }
        return $spots;
    }

    public function costSummaryPerTimeBelt($material_lengths, $vol_disc, $agency_comm)
    {
        $gross_value = 0;
        $net_value = 0;
        $savings = 0;
        $durations = [];

        foreach ($material_lengths as $key => $value) {
            $durations[] = $key;
            $gross_unit_rate = $value->unit_rate;
            $value_less = $gross_unit_rate * ((100 - $vol_disc) / 100);
            $net_unit_rate = $value_less * ((100 - $agency_comm) / 100);
            $spots = 0;

            foreach ($value->days as $date => $num_spots) {
                $spots += $num_spots;
            }

            $gross_value += ($gross_unit_rate * $spots);
            $net_value += ($net_unit_rate * $spots);
        }

        $savings = ($gross_value - $net_value);

        return json_encode(['gross_value' => $gross_value, 'net_value' => $net_value, 'savings' => $savings, 'durations' => $durations]);
    }

}