<?php

namespace Vanguard\Services\Ratings;

use Vanguard\Models\MpsProfile;

use DB;
use Illuminate\Support\Arr;
use Log;
use Vanguard\Libraries\DayPartList;
use Vanguard\Services\BaseServiceInterface;

/**
 * This class should given a few demographic parameters returns ratings grouped in a certain way
 * @todo log how long it takes to calculate these counts
 * Sample Query
 * '{"age":[{"min":"18","max":"45"},{"min":"30","max":"60"}],"state":["Abia","Abuja","Adamawa","AkwaIbom","Anambra","Bauchi","Bayelsa","Benue","Borno","CrossRiver","Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe","Zamfara"],"social_class":["A","B","C","D","E"],"gender":["Male","Female"],"region":["North-West","North-East (Jos)","North Central (Abuja)","South-West (Ibadan, Benin)","South-East (Onitsha, Aba)","South-South (Rivers)","Lagos"]}'
 */
class TvRatingsList implements BaseServiceInterface
{

    const DAY_CONVERSION = array(
        "Mon" => "Monday",
        "Tue" => "Tuesday",
        "Wed" => "Wednesday",
        "Thu" => "Thursday",
        "Fri" => "Friday",
        "Sat" => "Saturday",
        "Sun" => "Sunday"
    );

    protected $filters = [];
    protected $hash_key = '';

    /**
     * Demographics in this case should be an associative array of criteria such as:
     * {
     *  "age": ["12", "33"] ==> This should be a range
     *  "state": ["Abuja", "Kwara"]
     *  "social_class": ["A", "B", "C"],
     *  "gender": ["Male", "Female"]
     * }
     */
    public function __construct($filters) {
        $this->filters = $filters;
        $this->hash_key = $this->getHashKey();
    }

    /**
     * Convert the filter array to md5, this is the key that will be used to 
     * grab the ratings from cache/cache the ratings
     */
    protected function getHashKey() {
        $this->filters['query_type'] = $this->getQueryType();
        $filter_str = json_encode($this->filters);
        return md5($filter_str);
    }

    protected function getQueryType() {
        return "full_list";
    }

    public function run() {
        $expire_at = now()->addDays(7);
        $rating_data = cache()->remember($this->hash_key, $expire_at, function() {
            return $this->calculateRatings();
        });
        return $rating_data;
    }

    protected function calculateRatings() {
        $universe_size = $this->getUniverseSize();
        $query = $this->filterForRequestedAudience();

        $station_cols = 'ts.id as station_id, ts.type as station_type, ts.name as station_name, ts.state as station_state';
        $timebelt_cols = 'mpa.day, mpa.start_time, mpa.end_time, SUM(mps_profiles.pop_weight) as total_audience';

        $station_type = Arr::get($this->filters, 'station_type');

        $timebelt_results = $query->select(DB::raw("{$station_cols},{$timebelt_cols}"))
            ->join('tv_stations as ts', 'ts.key', '=', 'mpa.tv_station_key')
            ->when($station_type, function($query) use ($station_type) {
                $query->where('ts.type', strtolower($station_type));
            })
            ->groupBy('mpa.tv_station_key', 'mpa.day', 'mpa.start_time', 'mpa.end_time')
            ->get();
        $ratings = $this->generateRatings($timebelt_results, $universe_size);
        return collect($ratings);
    }

    /**
     * This should get the total count of the universe to use for ratings
     */
    protected function getUniverseSize() {
        return MpsProfile::sum('pop_weight');
    }

    /**
     * This should return a numerical array of sample counts by station and timebelt (which includes the day). i.e
     * [
     *   {"station": "ABC", "day": "Monday", "start_time": "12:00", "end_time": "12:15", "state": "Abuja", "total_audience": 1000000},
     *   {"station": "EFG", "day": "Friday", "start_time": "01:00", "end_time": "01:15", "state": "Kaduna", "total_audience": 5000000}
     * ]
     */
    protected function filterForRequestedAudience() {
        $days = Arr::get($this->filters, 'day');
        $day_parts = Arr::get($this->filters, 'day_part');
        $station_type = Arr::get($this->filters, 'station_type');
        return MpsProfile::filter($this->filters)
            ->join('mps_profile_activities as mpa', 'mpa.ext_profile_id', '=', 'mps_profiles.ext_profile_id')
            ->join('tv_stations as ts', 'ts.key', '=', 'mpa.tv_station_key')
            ->when($days, function($query) use ($days) {
                $query->where('mpa.day', $days);
            })
            ->when($station_type, function($query) use ($station_type) {
                $query->where('ts.type', strtolower($station_type));
            })
            ->when($day_parts, function($query) use ($day_parts) {
                $query->whereBetween("mpa.start_time", DayPartList::DAYPARTS[$day_parts]);
            });
    }

    private function generateRatings($timebelt_results, $universe_size) {
        $timebelt_results->transform(function($timebelt) use ($universe_size) {
            $total_audience = (double) $timebelt->total_audience;
            $rating = ($total_audience / $universe_size) * 100;
            return [
                "media_type" => "Tv", //Only tv is supported for now
                "station" => $timebelt->station_name,
                "station_id" => $timebelt->station_id,
                "station_type" => $timebelt->station_type,
                "station_state" => $timebelt->station_state,
                "day" => $timebelt->day,
                "start_time" => $timebelt->start_time,
                "end_time" => $timebelt->end_time,
                "total_audience" => $total_audience,
                "rating" => round($rating, 2)
            ];
        });
        return $timebelt_results->toArray();
    }
}