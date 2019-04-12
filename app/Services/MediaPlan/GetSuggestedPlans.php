<?php
namespace Vanguard\Services\MediaPlan;
use DB;

/**
 * This class should given a media plan id, grab the timebelts/stations that have already been created for it
 */
class GetSuggestedPlans
{

    //inclusive
    const DAYPARTS = array(
        "Late Night" => array("21:00", "00:00"),
        "Overnight" => array("00:00", "05:00"),
        "Breakfast" => array("05:00", "09:00"),
        "Late Breakfast" => array("09:00", "12:00"),
        "Afternoon" => array("12:00", "17:00"),
        "Primetime" => array("17:00", "21:00")
    );

    public function __construct($mediaPlanId, $filters=array()) {
        $this->mediaPlanId = $mediaPlanId;
        $this->filters = $filters;
    }

    public function get() {
        $plans = DB::table("media_plan_suggestions")
            ->select(DB::Raw("*, total_audience as audience"))
            ->where("media_plan_id", $this->mediaPlanId)
            ->when($this->filters, function($query) {
                foreach ($this->filters as $key => $value) {
                    if ($key == "dayparts") {
                        $query->whereBetween("start_time", static::DAYPARTS[$value]);
                        continue;
                    }
                    if (in_array($key, array("state", "day"))) {
                        $query->where("state", $value);
                        continue;
                    }
                }
            })->get();

            $selected_plans = DB::table("media_plan_suggestions")
            ->select(DB::Raw("*, total_audience as audience"))
            ->where("status", 1)->get();


        if ($plans->isEmpty()) {
            return array();
        }

        $total_audience = $plans->sum("total_audience");
        $output = array(
            "total_tv" => $total_audience,
            "total_radio" => 0,
            "total_audiences" => $total_audience,
            "programs_stations" => $plans->sortByDesc("total_audience"),
            "stations" => $plans->groupBy("station"),
            "selected" => $selected_plans->sortByDesc("total_audience")
        );
        return $output;
    }
}