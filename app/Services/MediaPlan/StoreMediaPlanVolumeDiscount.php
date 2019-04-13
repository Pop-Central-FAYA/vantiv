<?php

namespace Vanguard\Services\MediaPlan;

use Vanguard\Models\MediaPlanVolumeDiscount;

class StoreMediaPlanVolumeDiscount
{
    protected $discount;
    protected $station;
    protected $agency_id;

    public function __construct($dicount, $station, $agency_id)
    {
        $this->discount = $dicount;
        $this->station = $station;
        $this->agency_id = $agency_id;
    }

    private function deleteInstanceOfRecord()
    {
        return MediaPlanVolumeDiscount::where([
            ['station', $this->station],
            ['agency_id', $this->agency_id]
        ])->delete();
    }

    public function storeMediaPlanDiscount()
    {
        \DB::transaction(function () use(&$media_plan_volume_discount) {
            $this->deleteInstanceOfRecord();
            $media_plan_volume_discount = new MediaPlanVolumeDiscount();
            $media_plan_volume_discount->station = $this->station;
            $media_plan_volume_discount->agency_id = $this->agency_id;
            $media_plan_volume_discount->discount = $this->discount;
            $media_plan_volume_discount->save();
        });
        return $media_plan_volume_discount;
    }
}
