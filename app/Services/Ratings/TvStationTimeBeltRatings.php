<?php

namespace Vanguard\Services\Ratings;

use DB;
use Illuminate\Support\Arr;
use Log;

// 
/**
 * This class generates the ratings for a particular station and its timebelts
 * @todo log how long it takes to calculate these counts
 * Sample Query
 * '{"age":[{"min":"18","max":"45"},{"min":"30","max":"60"}],"state":["Abia","Abuja","Adamawa","AkwaIbom","Anambra","Bauchi","Bayelsa","Benue","Borno","CrossRiver","Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe","Zamfara"],"social_class":["A","B","C","D","E"],"gender":["Male","Female"],"region":["North-West","North-East (Jos)","North Central (Abuja)","South-West (Ibadan, Benin)","South-East (Onitsha, Aba)","South-South (Rivers)","Lagos"]}'
 * Sample cli command
 * $filters = ["gender" => ["Male", "Female"], "age" => [["min" => 18, "max" => 80]], "tv_station_key" => "5213314f77a5ac013f5f52bbf73e3b1d"]; $service = new \Vanguard\Services\Ratings\TvStationTimeBeltRatings($filters); $res = $service->run();
 */
class TvStationTimeBeltRatings extends TvRatingsList
{

    protected function getQueryType() {
        return "timebelt_level";
    }

    protected function calculateRatings() {
        $universe_size = $this->getUniverseSize();

        // DB::enableQueryLog();

        $tv_station_key = Arr::get($this->filters, 'tv_station_key');

        $station_cols = 'ts.name as station_name, ts.state as station_state, ts.id as station_id, ts.type as station_type';
        $sub_query_cols = 'mpa.tv_station_key, mpa.day, mpa.start_time, mpa.end_time, mps_profiles.pop_weight';
        $sub_query = $this->filterForRequestedAudience()
            ->select(DB::raw("{$sub_query_cols},{$station_cols}"))
            ->when($tv_station_key, function($query) use ($tv_station_key) {
                $query->where('mpa.tv_station_key', $tv_station_key);
            })
            ->groupBy('mpa.day', 'mpa.start_time', 'mpa.ext_profile_id');
        
        $final_station_cols = 'station_name, station_state, station_id, station_type';
        $query_cols = 'tv_station_key, day, start_time, end_time, SUM(tbl.pop_weight) as total_audience';
        $main_query = DB::query()->fromSub($sub_query, 'tbl')
            ->selectRaw("{$query_cols},{$final_station_cols}")
            ->groupBy("tbl.day", "tbl.start_time")
            ->orderBy('total_audience', 'desc');

        $timebelt_results = $main_query->get();

        // Log::info(DB::getQueryLog());

        $ratings = $this->generateRatings($timebelt_results, $universe_size);
        return collect($ratings);
    }

    protected function generateRatings($timebelt_results, $universe_size) {
        $timebelt_results->transform(function($timebelt) use ($universe_size) {
            $total_audience = (double) $timebelt->total_audience;
            $rating = ($total_audience / $universe_size) * 100;
            return [
                "day" => $timebelt->day,
                "start_time" => $timebelt->start_time,
                "end_time" => $timebelt->end_time,
                "total_audience" => $total_audience,
                "rating" => round($rating, 2),
                "station_key" => $timebelt->tv_station_key,
                "station_id" => $timebelt->station_id,
                "station" => $timebelt->station_name,
                "state" => $timebelt->station_state,
                "station_type" => $timebelt->station_type,
                "media_type" => 'tv'
            ];
        });
        return $timebelt_results->toArray();
    }
}
