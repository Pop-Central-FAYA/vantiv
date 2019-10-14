<?php

namespace Vanguard\Services\MediaPlan;

use Illuminate\Support\Arr;
use DB;
use Vanguard\Libraries\Enum\MediaPlanStatus;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\MediaPlanSuggestion;
use Vanguard\Services\BaseServiceInterface;

/**
 * This service is to create a Media Plan (This is only created if there is a rating available).
 */
class CloneMediaPlanService implements BaseServiceInterface
{   
    const DEFAULT_FILTERS = ['station_type' => 'network'];
    protected $company_id;
    protected $data;
    protected $media_plan;
    protected $user_id;

    public function __construct($media_plan, $data, $company_id, $user_id)
    {
        $this->media_plan = $media_plan;
        $this->data = $data;
        $this->company_id = $company_id;
        $this->user_id = $user_id;
    }

    public function run()
    {
        return $this->storeMediaPlan();
    }

    protected function storeMediaPlan()
    {
        return DB::transaction(function () {
            $cloned_media_plan = new MediaPlan();
            $cloned_media_plan->gender = $this->media_plan->gender;
            $cloned_media_plan->criteria_social_class = $this->media_plan->criteria_social_class;
            $cloned_media_plan->criteria_region = $this->media_plan->criteria_region;
            $cloned_media_plan->criteria_state = $this->media_plan->criteria_state;
            $cloned_media_plan->criteria_age_groups = $this->media_plan->criteria_age_groups;

            $cloned_media_plan->agency_commission = $this->media_plan['agency_commission'];
            $cloned_media_plan->start_date = $this->data['start_date'];
            $cloned_media_plan->end_date = $this->data['end_date'];
            $cloned_media_plan->media_type = $this->media_plan['media_type'];
            $cloned_media_plan->campaign_name = $this->data['campaign_name'];
            $cloned_media_plan->product_name = $this->data['product'];
            $cloned_media_plan->client_id = $this->data['client'];
            $cloned_media_plan->brand_id = $this->data['brand'];

            $cloned_media_plan->planner_id = $this->user_id;
            $cloned_media_plan->company_id = $this->company_id;
            $cloned_media_plan->status = MediaPlanStatus::PENDING;
            $cloned_media_plan->filters = json_encode(static::DEFAULT_FILTERS);
            $cloned_media_plan->state_list = json_encode([]);

            /** We can't set insertion summary during a clone
             * because insertions are computed by flight dates
             * Flight date of the cloned plan may be different from that of the existing plan
             *  */
            // $cloned_media_plan->target_population = $this->media_plan['target_population'];
            // $cloned_media_plan->population = $this->media_plan['population'];
            // $cloned_media_plan->gross_impressions = $this->media_plan['gross_impressions'];
            // $cloned_media_plan->total_insertions = $this->media_plan['total_insertions'];
            // $cloned_media_plan->net_reach = $this->media_plan['net_reach'];
            // $cloned_media_plan->net_media_cost = $this->media_plan['net_media_cost'];
            // $cloned_media_plan->cpm = $this->media_plan['cpm'];
            // $cloned_media_plan->cpp = $this->media_plan['cpp'];
            // $cloned_media_plan->avg_frequency = $this->media_plan['avg_frequency'];
            // $cloned_media_plan->total_grp = $this->media_plan['total_grp'];

            $cloned_media_plan->save();
            $this->storeSuggestions($cloned_media_plan);
            return $cloned_media_plan;
        });
    }

    protected function storeSuggestions($cloned_plan)
    {
        foreach ($this->media_plan->suggestions as $item) {
            $data = [
                'media_plan_id' => $cloned_plan['id'],
                'media_type' => $item['media_type'],
                'program' => $item['program'],
                'day' => $item['day'],
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time'],
                'total_audience' => $item['total_audience'],
                'station_id' => $item['station_id'],
                'rating' => $item['rating'],
                'material_length' => json_encode([]),
                //these are probably the fields that do not need to be present
                'status' => 1,
                'exposure_calculation' => '',
                'station' => $item['station'],
                'state' => $item['state'],
                'station_type' => $item['station_type'],
                'region' => '',
                'state_counts' => ''
            ];
            MediaPlanSuggestion::create($data);
        }
    }
}
