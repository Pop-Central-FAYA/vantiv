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
    }

    /**
     * For timebelts listing
     */
    protected function calculateRatings() {
        $universe_size = $this->getUniverseSize();

        $tv_station_key = Arr::get($this->filters, 'tv_station_key');

        $station_cols = 'ts.name as station_name, ts.state as station_state, ts.id as station_id, ts.type as station_type';
        $sub_query_cols = 'mpa.tv_station_key, mpa.day, mpa.start_time, mpa.end_time, mps_profiles.pop_weight';
        $sub_query = $this->filterForRequestedAudience()
            ->select(DB::raw("{$sub_query_cols},{$station_cols}"))
            ->when($tv_station_key, function($query) use ($tv_station_key) {
                $query->where('mpa.tv_station_key', $tv_station_key);
            })
            ->groupBy('mpa.tv_station_key', 'mpa.day', 'mpa.start_time', 'mpa.ext_profile_id');
        
        $final_station_cols = 'station_name, station_state, station_id, station_type';
        $query_cols = 'tv_station_key, day, start_time, end_time, SUM(tbl.pop_weight) as total_audience';
        $main_query = DB::query()->fromSub($sub_query, 'tbl')
            ->selectRaw("{$query_cols},{$final_station_cols}")
            ->groupBy('tbl.tv_station_key', "tbl.day", "tbl.start_time")
            ->orderBy('total_audience', 'desc');

        $timebelt_results = $main_query->get();

        $ratings = $this->generateRatings($timebelt_results, $universe_size);
        return collect($ratings);
    }

    // $profile_cols = "SUM(mp.pop_weight) as total_audience";
    // $activities_cols = "mpa.day, mpa.start_time, mpa.broadcast_type";
    // $station_cols = "ts.id as station_id, ts.type as station_type, ts.name as station_name, ts.state as station_state, ts.key as station_key";


    /**
     * For just stations listing
     */
    protected function calculateRatings() {
        $universe_size = $this->getUniverseSize();
        
        $sub_query_cols = 'ts.name, ts.state, ts.type, ts.key, mpa.tv_station_id, mps_profiles.pop_weight';
        $sub_query = $this->filterForRequestedAudience()
            ->select(DB::raw($sub_query_cols))
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

    public function run() {
        $query = $this->generateQuery();

        $raw_sql = Query::getSql($query);
        Log::info($raw_sql);
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
        $profile_model = new MpsProfile();
        $profile_table = $profile_model->getTable();

        $activities_model = new MpsProfileActivity();
        $activities_table = $activities_model->getTable();

        $station_model = new TvStation();
        $station_table = $station_model->getTable();

        $query = MpsProfile::from("{$profile_table} as mp")
                        ->filter($this->filters)
                        ->join("{$activities_table} as mpa", "mpa.ext_profile_id", "=", "mp.ext_profile_id")
                        ->join("{$station_table} as ts", "ts.key", "=", "mpa.tv_station_key");

        $profile_cols = "SUM(mp.pop_weight) as total_audience";
        $activities_cols = "mpa.day, mpa.start_time, mpa.broadcast_type";
        $station_cols = "ts.id as station_id, ts.type as station_type, ts.name as station_name, ts.state as station_state, ts.key as station_key";
        
        $query = $query->selectRaw("{$profile_cols},{$activities_cols},{$station_cols}");
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