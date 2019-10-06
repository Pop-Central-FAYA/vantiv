<?php

namespace Vanguard\Services\Ratings;

use DB;

// use Vanguard\Services\Ratings\GetStationReachService; $filters = ["state" => ["Abuja"], "social_class" => ["A", "B", "C"], "gender" => ["Male", "Female"]];$service = new GetStationReachService($filters); $service->run()
class GetStationReachService extends AbstractRatingService
{

    protected function modifyQuery($query)
    {
        $query = $query->groupBy("tv_stations.key", "mps_profile_activities.ext_profile_id");

        $columns = [
            "station_name", "station_state",  "station_type", "station_key", "station_id",
            DB::raw("SUM(pop_weight) as total_audience")
        ];

        $main_query = DB::query()->fromSub($query, "tbl")
                            ->addSelect($columns)
                            ->groupBy("station_key")
                            ->orderBy("total_audience", "DESC")
                            ->orderBy("station_name")
                            ->orderBy("station_state")
                            ->orderBy("station_type");
        return $main_query;
    }
    
    protected function formatResponse($data) 
    {
        $data->transform(function($station) {
            return [
                "media_type" => "Tv", //Only tv is supported for now
                "station" => $station->station_name,
                "station_id" => $station->station_id,
                "station_key" => $station->station_key,
                "station_type" => $station->station_type,
                "station_state" => $station->station_state,
                "total_audience" => round((double)$station->total_audience),
            ];
        });
        return $data->toArray();
    }
}