<?php
namespace Vanguard\Services\MediaPlan;
use DB;
use Log;

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
                    if ($key == "day_parts") {
                        $query->whereBetween("start_time", static::DAYPARTS[$value]);
                        continue;
                    }
                    if ($key == "days") {
                        $query->where("day", $value);
                        continue;
                    }
                    if ($key == "states") {
                        $query->where("state_counts", "LIKE", "%{$value}%");
                    }
                }
            })->get();
        
        if ($plans->isEmpty()) {
            return array(
                "total_tv" => 0,
                "total_radio" => 0,
                "total_audiences" => 0,
                "programs_stations" => collect([]),
                "stations" => collect([]),
                "selected" => collect([]),
                'total_graph' => collect([])
            );
        }

        $plans = $this->getCountsByState($plans);

        $selected_plans = DB::table("media_plan_suggestions")
            ->select(DB::Raw("*, total_audience as audience"))
            ->where("media_plan_id", $this->mediaPlanId)
            ->where("status", 1)->get();

        $total_audience = $plans->sum("total_audience");
        return array(
            "total_tv" => $total_audience,
            "total_radio" => 0,
            "total_audiences" => $total_audience,
            "programs_stations" => $plans->sortByDesc("total_audience"),
            "stations" => $plans->groupBy("station"),
            "selected" => $selected_plans->sortByDesc("total_audience"),
            'total_graph' => $plans->groupBy(['day', 'station'])
        );
    }

    /**
     *  state filter is different, cause the state counts are saved in a field on the suggestions table
    */
    protected function getCountsByState($plans) {
        if (isset($this->filters['states'])) {
            $state_val = $this->filters['states'];
            return $plans->map(function ($item, $key) use ($state_val) {
                $state_counts = json_decode($item->state_counts, true);
                $item->audience = $state_counts[$state_val];
                return $item;
            });
        }
        return $plans;
    }
}
