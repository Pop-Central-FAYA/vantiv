<?php

namespace Vanguard\Services\MediaPlan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;

class SummarizePlan
{
    protected $media_plan;

    public function __construct($media_plan)
    {
        $this->media_plan = $media_plan;    
    }

    public function run()
    {
        $media_plan_start_date = Carbon::parse($this->media_plan->start_date);
        $media_plan_end_date = Carbon::parse($this->media_plan->end_date);
        $media_plan_period = $media_plan_start_date->diffInWeeks($media_plan_end_date);
        $media_plan_monthly_weeks_header = $this->groupPeriodIntoMonthWeek($this->media_plan->start_date, $this->media_plan->end_date);
        $media_plan_suggestions = $this->getMediaPlanSuggestions($this->media_plan);
        $plan_summary = $this->summarizeMediaPlan($media_plan_suggestions);
        // Group suggestion by media type and material duration
        $media_plan_grouped_data = $this->groupByMediumMaterialDuration($media_plan_suggestions);
        $summary_by_medium = $this->generateMediumSummary($media_plan_grouped_data, 1);
        return collect([
            'plan' => $this->media_plan,
            'period' => $media_plan_period,
            'summary' => $plan_summary,
            'table_header_monthly_weeks' => $media_plan_monthly_weeks_header,
            'summary_by_medium' => $summary_by_medium
        ]);
    }

    public function generateMediumSummary($media_plan_grouped_data, $media_plan_period) {
        $summary_by_medium = [];
        foreach ($media_plan_grouped_data as $medium => $durations_data) {
            $summary_by_medium[$medium] = [];
            $summary_by_medium[$medium]['summary'] = [
                'summary_by_duration' => $this->getMediumSummaryByduration($durations_data, $media_plan_period),
                'summary_by_station_type' => $this->getMediumSummaryByStationType($durations_data)
            ];
            foreach ($durations_data as $duration => $time_belts) {
                $summary_by_medium[$medium][$duration] = $time_belts->groupBy(['station_type', 'station']);
            }
        }
        return $summary_by_medium;
    }

    public function getMediumSummaryByduration($material_durations, $media_plan_period) {
        $summary = [];
        foreach ($material_durations as $length => $timebelts) {
            $summary['data'][] = collect([
                'length' => $length,
                'total_spots' => $timebelts->sum('total_spots'),
                'gross_total' => $timebelts->sum('gross_value'),
                'net_total' => $timebelts->sum('net_value'),
                'duration' => $media_plan_period
            ]);
        }
        $summary['totals'] = [
            'total_spots' => collect($summary['data'])->sum('total_spots'),
            'gross_total' => collect($summary['data'])->sum('gross_total'),
            'net_total' => collect($summary['data'])->sum('net_total'),
            'vat' => collect($summary['data'])->sum('net_total') * 0.05
        ]; 
        return $summary;
    }

    public function getMediumSummaryByStationType($material_durations) {
        $summary = [];
        foreach ($material_durations as $length => $timebelts) {
            $group_by_station_type = $timebelts->groupBy('station_type');
            foreach ($group_by_station_type as $station_type => $value) {
                $summary['data'][] = collect([
                    'duration' => $length, 'station_type' => $station_type, 
                    'total_spots' => $value->sum('total_spots'),
                    'net_total' => $value->sum('net_value'),
                ]);
            }
        }
        $summary['totals'] = [
            'total_spots' => collect($summary['data'])->sum('total_spots'),
            'net_total' => collect($summary['data'])->sum('net_total')
        ]; 
        return $summary;
    }

    public function getMediaPlanSuggestions($mediaPlan) {
        $selected_suggestions = $mediaPlan->suggestions->where('status', 1);
        return $selected_suggestions->map(function($item, $key) use ($mediaPlan) {
                        $material_lengths = $this->customMaterialDurations(json_decode($item->material_length));
                        $item->num_spots = $this->numSpotsPerStationTimebelt($material_lengths);
                        $agency_commission = $mediaPlan->agency_commission;
                        $cost_summary = json_decode($this->costSummaryPerTimeBelt($material_lengths, $agency_commission));
                        $item->gross_value = $cost_summary->gross_value;
                        $item->net_value = $cost_summary->net_value;
                        $item->savings = $cost_summary->savings;
                        $item->material_durations = array_keys((array)$cost_summary->durations);
                        $item->material_lengths = $cost_summary->durations;
                        $item->week_days = $this->daysOfTheWeek($item->day);
                        return $item;
                    });
    }

    public function summarizeMediaPlan($suggestions)
    {
        $group_summary_by_media_type = $suggestions->groupBy('media_type');
        return $this->formatSummaryData($group_summary_by_media_type);
    }

    public function formatSummaryData($data) {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = (object)[
                'medium' => $key,
                'gross_value' => $value->sum('gross_value'),
                'net_value' => $value->sum('net_value'),
                'savings' => $value->sum('savings'),
                'total_spots' => $value->sum('num_spots'),
                'material_durations' => $value->pluck('material_durations')->collapse()->unique() 
            ];
        }
        return $result;
    }

    public function groupByMediumMaterialDuration($suggestions) {
        $new_array = [];
        foreach ($suggestions as $suggestion) {
            foreach ($suggestion->material_lengths as $key => $value) {
                $new_array[] = [
                    'material_duration' => $key,
                    'station' => $suggestion->station,
                    'station_type' => $suggestion->station_type,
                    'station_region' => $suggestion->region,
                    'program' => $suggestion->program.' '.date('h:ia', strtotime($suggestion->start_time)).' - '.date('h:ia', strtotime($suggestion->end_time)),
                    'week_days' => $suggestion->week_days,
                    'media_type' => $suggestion->media_type,
                    'gross_unit_rate' => $value->gross_unit_rate,
                    'volume_discount' => $value->volume_discount,
                    'agency_commission' => $value->agency_commission,
                    'value_less' => $value->value_less,
                    'net_unit_rate' => $value->net_unit_rate,
                    'total_spots' => $value->total_spots,
                    'bonus_spots' => 0,
                    'cost_bonus_spots' => 0,
                    'gross_value' => $value->gross_value,
                    'net_value' => $value->net_value,
                    'net_value_after_bonus_spots' => $value->net_value_after_bonus_spots,
                    'month_weeks' => $value->month_weeks
                ];
            }
        }
        return collect($new_array)->groupBy(['media_type','material_duration']);
    }

    public function groupPeriodIntoMonthWeek($start_date, $end_date)
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
                $new_dates_array[$month][] = (Object)[ 'week' => $key, 'slot' => 0 ];
            }
        }
        return $new_dates_array;
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

    public function costSummaryPerTimeBelt($material_lengths, $agency_comm)
    {
        $gross_value = 0; $net_value = 0; $savings = 0; $durations = [];

        foreach ($material_lengths as $key => $value) {
            // $durations[] = $key;
            $gross_unit_rate = (int) $value->unit_rate;
            $value_less = $gross_unit_rate * ((100 - (int)$value->volume_disc) / 100);
            $net_unit_rate = $value_less * ((100 - (int)$agency_comm) / 100);
            $spots = 0;
            foreach ($value->days as $date => $num_spots) {
                $spots += $num_spots;
            }
            $gross_value += ($gross_unit_rate * $spots);
            $net_value += ($net_unit_rate * $spots);

            $durations[$key] = [
                "gross_unit_rate" => $gross_unit_rate,
                "volume_discount" => (int)$value->volume_disc,
                "agency_commission" => (int)$agency_comm,
                "value_less" => $value_less,
                "net_unit_rate" => $net_unit_rate,
                "total_spots" => $spots,
                "bonus_spots" => 0,
                "cost_bonus_spots" => 0,
                "gross_value" => $gross_value,
                "net_value" => $net_value,
                "net_value_after_bonus_spots" => $net_value,
                'month_weeks' => $this->monthWeeklySlots($value->days, $this->media_plan->start_date, $this->media_plan->end_date)
            ];
        }

        $savings = ($gross_value - $net_value);

        return json_encode(['gross_value' => $gross_value, 'net_value' => $net_value, 'savings' => $savings, 'durations' => $durations]);
    }

    public function daysOfTheWeek($letter_day)
    {
        $custom_days = [ 'M' => 0, 'TU' => 0, 'W' => 0, 'TH' => 0, 'F' => 0, 'SA' => 0, 'SU' => 0 ];
        $days = [ 'Monday' => 'M', 'Tuesday' => 'TU', 'Wednesday' => 'W', 'Thursday' => 'TH', 'Friday' => 'F', 'Saturday' => 'SA', 'Sunday' => 'SU' ];

        if (array_key_exists($letter_day, $days)){
            $custom_day = $days[$letter_day];
            $custom_days[$custom_day] = 1;
        }
        return $custom_days;
    }

    public function monthWeeklySlots($selected_dates, $plan_start_date, $plan_end_date)
    {
        $month_weeks = $this->groupPeriodIntoMonthWeek($plan_start_date, $plan_end_date);
        foreach ($selected_dates as $date => $slots) {
            $date = new DateTime($date);
            $selected_week = $date->format('W');
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
}