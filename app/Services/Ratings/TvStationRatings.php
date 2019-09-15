<?php

namespace Vanguard\Services\Ratings;

use DB;
use Illuminate\Support\Arr;

// 
/**
 * This class generates the station level ratings
 * Sample Query
 * '{"age":[{"min":"18","max":"45"},{"min":"30","max":"60"}],"state":["Abia","Abuja","Adamawa","AkwaIbom","Anambra","Bauchi","Bayelsa","Benue","Borno","CrossRiver","Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe","Zamfara"],"social_class":["A","B","C","D","E"],"gender":["Male","Female"],"region":["North-West","North-East (Jos)","North Central (Abuja)","South-West (Ibadan, Benin)","South-East (Onitsha, Aba)","South-South (Rivers)","Lagos"]}'
 * Sample cli command
 * $filters = ["station_type" => "network", "gender" => ["Male", "Female"], "age" => [["min" => 18, "max" => 80]]]; $service = new \Vanguard\Services\Ratings\TvStationRatings($filters); $res = $service->run();
 */
class TvStationRatings extends TvRatingsList
{

    protected function getQueryType() {
        return "station_level";
    }
    
    protected function calculateRatings() {
        $universe_size = $this->getUniverseSize();

        $station_type = Arr::get($this->filters, 'station_type');
        
        $sub_query_cols = 'ts.name, ts.state, ts.type, ts.key, mpa.tv_station_id, mps_profiles.pop_weight';
        $sub_query = $this->filterForRequestedAudience()
            ->select(DB::raw($sub_query_cols))
            ->join('tv_stations as ts', 'ts.key', '=', 'mpa.tv_station_key')
            ->when($station_type, function($query) use ($station_type) {
                $query->where('ts.type', strtolower($station_type));
            })
            ->groupBy('mpa.tv_station_key', 'mpa.ext_profile_id');

        $query_cols = 'name as station_name, state as station_state, type as station_type, `key`, tv_station_id, SUM(pop_weight) as total_audience';
        $main_query = DB::query()->fromSub($sub_query, 'tbl')
            ->selectRaw($query_cols)
            ->groupBy("key")
            ->orderBy('total_audience', 'desc');
        $timebelt_results = $main_query->get();

        $ratings = $this->generateRatings($timebelt_results, $universe_size);
        return collect($ratings);
    }

    protected function generateRatings($timebelt_results, $universe_size) {
        $timebelt_results->transform(function($timebelt) use ($universe_size) {
            return [
                "media_type" => "Tv", //Only tv is supported for now
                "station" => $timebelt->station_name,
                "station_id" => $timebelt->tv_station_id,
                "station_key" => $timebelt->key,
                "station_type" => $timebelt->station_type,
                "station_state" => $timebelt->station_state,
                "total_audience" => round((double)$timebelt->total_audience),
            ];
        });
        return $timebelt_results->toArray();
    }
}