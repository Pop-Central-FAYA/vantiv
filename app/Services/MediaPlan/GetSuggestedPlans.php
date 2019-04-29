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

    // const STATION_TYPE = array(
    //     "Cable" => "cable",
    //     "Network" => "network",
    //     "Terrestrial" => "terrestrial"
    // );

    const STATION_TYPE = array(
        "Network" => "network",
        "Regional" => "regional",
        "Satellite" => "satellite"
    );

    public function __construct($mediaPlanId, $filters=array()) {
        $this->mediaPlanId = $mediaPlanId;
        if (!isset($filters['station_type'])) {
            $filters = array("station_type" => "Network");
        }
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
                    if ($key == "station_type") {
                        $query->where("station_type", static::STATION_TYPE[$value]);
                        continue;
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
                'total_graph' => collect([]),
                'days' => array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")
            );
        }

        $plans = $this->getCountsByState($plans);

        $selected_plans = DB::table("media_plan_suggestions")
            ->select(DB::Raw("*, total_audience as audience, CONCAT(station, ' - ', state) as station_state"))
            ->where("media_plan_id", $this->mediaPlanId)
            ->where("status", 1)->get()
            ;

        $total_audience = $plans->sum("total_audience");
        return array(
            "total_tv" => $total_audience,
            "total_radio" => 0,
            "total_audiences" => $total_audience,
            "programs_stations" => $plans->sortByDesc("total_audience"),
            "stations" => $this->groupbyState($plans),
            "selected" => $this->mapByStateSelected($selected_plans)->sortByDesc("total_audience")->values()->all(),
            'total_graph' => $plans->groupBy(['day', 'station']),
            'days' => array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")
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


protected function groupbyState($plans){

    $grouped = $plans->groupBy(function ($item, $key) {
        if( $item->state !== ""){
        return $item->station. " - ". $item->state;
        }
        return $item->station;
     });
    

    $grouped->toArray();
    return $grouped;
}

protected function mapByStateSelected($selected_plans){
        $concantinate = $selected_plans->map(function ($item, $key) {
            if($item->state !== ""){
                $item->station = $item->station. " - " .$item->state;
            }
            return $item;
        });
        return $concantinate;

}
}
