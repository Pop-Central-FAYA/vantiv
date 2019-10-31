<?php

namespace Vanguard\Services\MediaPlan;

use Vanguard\Services\BaseServiceInterface;

class UpdateMediaPlanService implements BaseServiceInterface
{
    protected $media_plan;
    protected $data;

    public function __construct($data, $media_plan)
    {
        $this->media_plan = $media_plan;
        $this->data = $data;
    }

    public function run()
    {
        return $this->update();
    }

    protected function update()
    {
        $this->media_plan->agency_commission = $this->data['agency_commission'];
        $this->media_plan->start_date = $this->data['start_date'];
        $this->media_plan->end_date = $this->data['end_date'];
        $this->media_plan->campaign_name = $this->data['campaign_name'];
        $this->media_plan->product_name = $this->data['product'];
        $this->media_plan->client_id = $this->data['client'];
        $this->media_plan->brand_id = $this->data['brand'];
        $this->media_plan->save();
        return $this->media_plan;
    }
}