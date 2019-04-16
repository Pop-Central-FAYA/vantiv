<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Yajra\DataTables\DataTables;
use Auth; 
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use DateTime;

class ExportPlan
{
    protected $media_plan;

    public function __construct($media_plan)
    {
        $this->media_plan = $media_plan;    
    }

    public function run()
    {
        return $this->exportPlan($this->media_plan);
    }

    public function exportPlan($mediaPlan)
    {
        $selectedSuggestions = $mediaPlan->suggestions->where('status', 1);
        $suggestions = $selectedSuggestions->map(function($item, $key) use ($mediaPlan) {
                        $item->material_length = $this->customMaterialDurations(json_decode($item->material_length));
                        return $item;
                    });

        $agency_commission = $mediaPlan->agency_commission;
        $plan_start_date = $mediaPlan->start_date;
        $plan_end_date = $mediaPlan->end_date;

        $new_array = [];

        foreach ($suggestions as $suggestion) {
            foreach ($suggestion->material_length as $key => $value) {
                $gross_unit_rate = (int) $value->unit_rate;
                $volume_discount = (int) $value->volume_disc;
                $value_less = $gross_unit_rate * ((100 - $volume_discount) / 100);
                $net_unit_rate = $value_less * ((100 - $agency_commission) / 100);
                $total_spots = $this->totalSpotsPerDuration($value->days);

                $new_array[] = [
                    'material_duration' => $key,
                    'station' => $suggestion->station,
                    'program' => $suggestion->program.' '.date('ha', strtotime($suggestion->start_time)).' - '.date('ha', strtotime($suggestion->end_time)),
                    'week_days' => $this->days_of_the_week($suggestion->day),
                    'media_type' => $suggestion->media_type,
                    'gross_unit_rate' => $gross_unit_rate,
                    'volume_discount' => $volume_discount,
                    'agency_commission' => $agency_commission,
                    'value_less' => $value_less,
                    'net_unit_rate' => $net_unit_rate,
                    'total_spots' => $total_spots,
                    'bonus_spots' => 0,
                    'cost_bonus_spots' => 0,
                    'gross_value' => $gross_unit_rate * $total_spots,
                    'net_value' => $net_unit_rate * $total_spots,
                    'net_value_after_bonus_spots' => $net_unit_rate * $total_spots,
                    'month_weeks' => $this->months_weekly_slots($value->days, $plan_start_date, $plan_end_date)
                ];
            }
        }

        return $this->groupByMediumMaterialLength($new_array);
    }

    public function groupByMediumMaterialLength($data)
    {
        $data = collect($data);
        return $data->groupBy(['media_type','material_duration','station']);

    }

    public function days_of_the_week($letter_day)
    {
        $custom_days = [
            'M' => 0,
            'TU' => 0,
            'W' => 0,
            'TH' => 0,
            'F' => 0,
            'SA' => 0,
            'SU' => 0
        ];

        $days = [
            'Monday' => 'M',
            'Tuesday' => 'TU',
            'Wednesday' => 'W',
            'Thursday' => 'TH',
            'Friday' => 'F',
            'Saturday' => 'SA',
            'Sunday' => 'SU'
        ];

        if (array_key_exists($letter_day, $days)){
            $custom_day = $days[$letter_day];
            $custom_days[$custom_day] = 1;
        }

        return $custom_days;
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
                $new_array[$key]['days'] = $dates;
            }
        }
        return json_decode(json_encode($new_array));
    }

    public function months_weekly_slots($selected_dates, $plan_start_date, $plan_end_date)
    {
        $month_weeks = $this->monthly_weeks_campaign_duration($plan_start_date, $plan_end_date);

        foreach ($selected_dates as $date => $slots) {
            //get week number from $date
            $date = new DateTime($date);
            $selected_week = $date->format('W');
            //find in array $month_week selected_week and update the number of slots
            $month_weeks = $this->updateSlotsPerWeek($month_weeks, $selected_week, $slots);
        }

        return $month_weeks;
    }

    public function updateSlotsPerWeek($monthly_weeks, $selected_week, $slots)
    {
        foreach ($monthly_weeks as $month => $weeksArr) {
            foreach ($weeksArr as $key=>$weekArr) {
                if ($weekArr->week == $selected_week) {
                    $weekArr->slot += $slots;
                }
            }
        }
        return $monthly_weeks;
    }

    public function monthly_weeks_campaign_duration($start_date, $end_date)
    {
        $dates = [];
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $dates[] =  [
                'actual_date' => $date->format('Y-m-d'),
                'month' => $date->format('M'),
                'week' => $date->format('W')
            ];
        }

        $dates = collect($dates);

        $dates = $dates->groupBy(['month', function ($item) {
                return $item['week'];
        }]);

        $new_dates_array = [];

        foreach ($dates as $month => $weeksArr) {
            foreach ($weeksArr as $key => $value) {
                $new_dates_array[$month][] = [ 'week' => $key, 'slot' => 0 ];
            }
        }
        return json_decode(json_encode($new_dates_array));
    }
}