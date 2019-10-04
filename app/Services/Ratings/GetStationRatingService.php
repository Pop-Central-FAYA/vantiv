<?php

namespace Vanguard\Services\Ratings;

use Vanguard\Models\MpsProfile;

use DB;
use Illuminate\Support\Arr;
use Log;
use Vanguard\Libraries\DayPartList;
use Vanguard\Libraries\Query;
use Vanguard\Models\MpsProfileActivity;
use Vanguard\Models\TvStation;
use Vanguard\Services\BaseServiceInterface;

// \DB::listen(function($sql, $bindings, $time) {
//     var_dump($sql);
//     var_dump($bindings);
//     var_dump($time);
// });

// DB::listen(function ($query) {
//     $query->sql;
//     $query->bindings;
//     $query->time;
// });

/**
 * This class should given a few demographic parameters returns ratings grouped in a certain way
 * @todo log how long it takes to calculate these counts
 * Sample Query
 * '{"age":[{"min":"18","max":"45"},{"min":"30","max":"60"}],"state":["Abia","Abuja","Adamawa","AkwaIbom","Anambra","Bauchi","Bayelsa","Benue","Borno","CrossRiver","Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe","Zamfara"],"social_class":["A","B","C","D","E"],"gender":["Male","Female"],"region":["North-West","North-East (Jos)","North Central (Abuja)","South-West (Ibadan, Benin)","South-East (Onitsha, Aba)","South-South (Rivers)","Lagos"]}'
 */
class GetStationRatingService implements BaseServiceInterface
{

    protected $filters = [];
    protected $profile_tbl_name = "";
    protected $activities_tbl_name = "";
    protected $station_tbl_name = "";

    // $filters = ["state" => ["Abuja"], "social_class" => ["A", "B", "C"], "gender" => ["Male", "Female"]];
    /**
     * Demographics in this case should be an associative array of criteria such as:
     * {
     *  "age": ["12", "33"] ==> This should be a range
     *  "state": ["Abuja", "Kwara"]
     *  "social_class": ["A", "B", "C"],
     *  "gender": ["Male", "Female"]
     * }
     */
    public function __construct(array $filters) 
    {
        $this->filters = $filters;

        $this->profile_tbl_name = "mps_profiles";
        $this->activities_tbl_name = "mps_profile_activities";
        $this->station_tbl_name = "tv_stations";
    }

    public function run() {
        $query = $this->generateQuery();
        $query = $this->modifyQuery($query);
        $raw_sql = Query::getSql($query);

        Log::info($raw_sql);
        dd('done');
        $hash_key = $this->generateHash($raw_sql);
        $expire_at = now()->addDays(7);

        // return cache()->remember($hash_key, $expire_at, function() use ($query) {
        //     $res = $query->get();
        //     return $this->formatResult($res);
        // });
    }

    /**
     * This should just return a query object which has been properly generated with the proper bindings etc
     */
    protected function generateQuery() {
       
        $query = MpsProfile::filter($this->filters)
                        ->join("mps_profile_activities", "mps_profile_activities.ext_profile_id", "=", "mps_profiles.ext_profile_id")
                        ->join("tv_stations", "tv_stations.key", "=", "mps_profile_activities.tv_station_key");

        $activities_cols = [
            "mps_profile_activities.day", "mps_profile_activities.start_time", "mps_profile_activities.broadcast_type"
        ];
        $station_cols = [
            "tv_stations.id as station_id", "tv_stations.type as station_type", "tv_stations.name as station_name", 
            "tv_stations.state as station_state", "tv_stations.key as station_key"
        ];
        $profile_cols = ["mps_profiles.pop_weight"];

        $query = $query->addSelect($activities_cols)
                        ->addSelect($station_cols)
                        ->addSelect($profile_cols);
        return $query;
    }

    /**
     * This is the method that will be overwritten to add extra clauses, select fields 
     * to the generic generated query.
     * For instance, depending on the request type, the groupBy's can be different
     * The fields returned can be different etc
     */
    protected function modifyQuery($query)
    {
        $query = $query->groupBy("tv_stations.key", "mps_profile_activities.ext_profile_id");
        return $query;
    }

    // private function generateHash($query)
    // {
    //     return md5($query);
    // }

    // protected function calculateRatings() {
    //     $universe_size = $this->getUniverseSize();
    //     $query = $this->filterForRequestedAudience();

    //     $station_cols = 'ts.id as station_id, ts.type as station_type, ts.name as station_name, ts.state as station_state';
    //     $timebelt_cols = 'mpa.day, mpa.start_time, mpa.end_time, SUM(mps_profiles.pop_weight) as total_audience';

    //     $station_type = Arr::get($this->filters, 'station_type');

    //     $timebelt_results = $query->select(DB::raw("{$station_cols},{$timebelt_cols}"))
    //         ->join('tv_stations as ts', 'ts.key', '=', 'mpa.tv_station_key')
    //         ->when($station_type, function($query) use ($station_type) {
    //             $query->where('ts.type', strtolower($station_type));
    //         })
    //         ->groupBy('mpa.tv_station_key', 'mpa.day', 'mpa.start_time', 'mpa.end_time')
    //         ->get();
    //     $ratings = $this->generateRatings($timebelt_results, $universe_size);
    //     return collect($ratings);
    // }

    // /**
    //  * This should get the total count of the universe to use for ratings
    //  */
    // protected function getUniverseSize() {
    //     return MpsProfile::sum('pop_weight');
    // }

    

    // private function generateRatings($timebelt_results, $universe_size) {
    //     $timebelt_results->transform(function($timebelt) use ($universe_size) {
    //         $total_audience = (double) $timebelt->total_audience;
    //         $rating = ($total_audience / $universe_size) * 100;
    //         return [
    //             "media_type" => "Tv", //Only tv is supported for now
    //             "station" => $timebelt->station_name,
    //             "station_id" => $timebelt->station_id,
    //             "station_type" => $timebelt->station_type,
    //             "station_state" => $timebelt->station_state,
    //             "day" => $timebelt->day,
    //             "start_time" => $timebelt->start_time,
    //             "end_time" => $timebelt->end_time,
    //             "total_audience" => $total_audience,
    //             "rating" => round($rating, 2)
    //         ];
    //     });
    //     return $timebelt_results->toArray();
    // }
}