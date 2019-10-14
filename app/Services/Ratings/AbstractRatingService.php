<?php

namespace Vanguard\Services\Ratings;

use Vanguard\Services\BaseServiceInterface;
use Vanguard\Models\MpsProfile;
use Vanguard\Libraries\Query;
use Log;

/**
 * This class should given a few demographic parameters returns ratings grouped in a certain way
 * @todo log how long it takes to calculate these counts
 * Sample Query
 * '{"age":[{"min":"18","max":"45"},{"min":"30","max":"60"}],"state":["Abia","Abuja","Adamawa","AkwaIbom","Anambra","Bauchi","Bayelsa","Benue","Borno","CrossRiver","Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe","Zamfara"],"social_class":["A","B","C","D","E"],"gender":["Male","Female"],"region":["North-West","North-East (Jos)","North Central (Abuja)","South-West (Ibadan, Benin)","South-East (Onitsha, Aba)","South-South (Rivers)","Lagos"]}'
 */
abstract class AbstractRatingService implements BaseServiceInterface
{

    protected $filters = [];
    protected $media_plan = null;
    
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
    public function __construct(array $filters, $media_plan) 
    {
        $this->filters = $filters;
        $this->media_plan = $media_plan;
    }

    public function run() {
        $query = $this->generateQuery();
        $query = $this->modifyQuery($query);
        $raw_sql = Query::getSql($query);

        Log::debug($raw_sql);

        $hash_key = $this->generateHash($raw_sql);
        $expire_at = now()->addDays(7);

        $query_res = cache()->remember($hash_key, $expire_at, function() use ($query) {
            return $query->get();
        });
        return collect($this->formatResponse($query_res));
    }

    /**
     * This is the main query that all queries should have (This filters the audience table etc)
     */
    private function generateQuery() {

        $activities_cols = [
            "mps_profile_activities.day", "mps_profile_activities.start_time", "mps_profile_activities.broadcast_type",
            "mps_profile_activities.end_time"
        ];
        $station_cols = [
            "tv_stations.id as station_id", "tv_stations.type as station_type", "tv_stations.name as station_name", 
            "tv_stations.state as station_state", "tv_stations.key as station_key"
        ];
        $profile_cols = ["mps_profiles.pop_weight"];
        
        $query = MpsProfile::filter($this->filters)
                        ->join("mps_profile_activities", "mps_profile_activities.ext_profile_id", "=", "mps_profiles.ext_profile_id")
                        ->join("tv_stations", "tv_stations.key", "=", "mps_profile_activities.tv_station_key")
                        ->addSelect($activities_cols)
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
    abstract protected function modifyQuery($query);

    private function generateHash($raw_sql) {
        return md5($raw_sql);
    }
    
    abstract protected function formatResponse($data);
}