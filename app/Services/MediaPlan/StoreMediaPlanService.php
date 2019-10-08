<?php

namespace Vanguard\Services\MediaPlan;

use Illuminate\Support\Arr;
use DB;
use Vanguard\Libraries\Enum\MediaPlanStatus;
use Vanguard\Models\MediaPlan;
use Vanguard\Services\Ratings\TvRatingsCount;
use Vanguard\Services\BaseServiceInterface;

/**
 * This service is to create a Media Plan (This is only created if there is a rating available).
 */
class StoreMediaPlanService implements BaseServiceInterface
{   
    const DEFAULT_FILTERS = ['station_type' => 'network'];

    protected $company_id;
    protected $data;
    protected $user_id;

    public function __construct($data, $company_id, $user_id)
    {
        $this->data = $data;
        $this->company_id = $company_id;
        $this->user_id = $user_id;
    }

    /**
     * Run a query to verify that the media plan will have results before creating the plan
     */
    public function run()
    {
        $ratings_service = new TvRatingsCount($this->data);
        $num_timebelts = $ratings_service->run();
        if ($num_timebelts > 0) {
            return $this->storeMediaPlan();
        }
        return null;
    }

    protected function storeMediaPlan()
    {
        return DB::transaction(function () {
            $media_plan = new MediaPlan();
            $media_plan->gender = json_encode(Arr::get($this->data, 'gender', []));
            $media_plan->criteria_social_class = json_encode(Arr::get($this->data, 'social_class', []));
            $media_plan->criteria_region = json_encode(Arr::get($this->data, 'region', []));
            $media_plan->criteria_state = json_encode(Arr::get($this->data, 'state', []));
            $media_plan->criteria_age_groups = json_encode(Arr::get($this->data, 'age_groups', []));

            $media_plan->agency_commission = $this->data['agency_commission'];
            $media_plan->start_date = $this->data['start_date'];
            $media_plan->end_date = $this->data['end_date'];
            $media_plan->media_type = $this->data['media_type'];
            $media_plan->campaign_name = $this->data['campaign_name'];
            $media_plan->product_name = $this->data['product'];
            $media_plan->client_id = $this->data['client'];
            $media_plan->brand_id = $this->data['brand'];

            $media_plan->planner_id = $this->user_id;
            $media_plan->company_id = $this->company_id;
            $media_plan->status = MediaPlanStatus::SUGGESTED;
            $media_plan->filters = json_encode(static::DEFAULT_FILTERS);
            $media_plan->state_list = json_encode([]);

            $media_plan->save();
            return $media_plan;
        });
    }
}
