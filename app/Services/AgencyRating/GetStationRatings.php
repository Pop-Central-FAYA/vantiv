<?php

namespace Vanguard\Services\AgencyRating;

use Vanguard\Models\MpsAudience;
use Vanguard\Models\MpsAudienceProgramActivity;
use Vanguard\Models\StatePopulation;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\MediaPlanProgram;
use Vanguard\Models\MediaPlanSuggestion;
use Vanguard\Models\Station;

use Vanguard\Libraries\Utilities;

use Illuminate\Support\Collection;

use Auth;
use Log;
use DB;
// use Batch;

// use Vanguard\Libraries\Batch\LaravelBatch;
// use Vanguard\\Libraries\\Batch\\LaravelBatch;
 
// use Mavinoo\LaravelBatch as Batch;
/**
 * This class should given a few demographic parameters returns ratings grouped in a certain way
 * @todo log how long it takes to calculate these counts
 */
class GetStationRatings
{

    const SUPPORTED_CRITERIA = array("age", "state", "social_class", "gender", "lsm", "region");

    const DAY_CONVERSION = array(
        "Mon" => "Monday",
        "Tue" => "Tuesday",
        "Wed" => "Wednesday",
        "Thu" => "Thursday",
        "Fri" => "Friday",
        "Sat" => "Saturday",
        "Sun" => "Sunday"
    );

    /**
     * Demographics in this case should be an associative array of criteria such as:
     * {
     *  "age": ["12", "33"] ==> This should be a range
     *  "state": ["Abuja", "Kwara"]
     *  "social_class": ["A", "B", "C"],
     *  "gender": ["Male", "Female"]
     * }
     */
    public function __construct($criteria, $request) {
        $this->criteria = $this->formatCriteria($criteria);
        $this->stationListing = array();
        $this->programNameMap = array();
        $this->criteriaForm = $request;
    }

    public function getRatings() {
        $counts_by_state = $this->getCountsByState();
        $timebelt_counts = $this->getTimeBeltsByCriteria();
        $results = $this->analyzeAndFormatResults($timebelt_counts, $counts_by_state);
        $new_media_plan = $this->runInsertQuery($results['state_list'], $results['projected_counts']);
        Log::debug($new_media_plan);
        return $new_media_plan;
        // return $results;
    }

    /**
     * Only send back criteria that are supported by this get ratings service
     * @todo Log as a warning whenever we encounter criteria that is not supported, but do not throw an exception 
     * @todo Let the requester send all the criteria they want (instead of using all or both)
     */
    private function formatCriteria($input_criteria) {
        $formatted_criteria = array();
        foreach (static::SUPPORTED_CRITERIA as $supported_criteria) {
            if (isset($input_criteria[$supported_criteria])) {
                $value = $input_criteria[$supported_criteria];
                /**
                 * If the user selected all or both, then they want all the values, so just unset
                 * so the query gets all the values.
                 * This assumes that there are no blank values in the audience db (which according to the data there should not be)
                 */
                if (!in_array($value, ['All', 'Both'])) {
                    $formatted_criteria[$supported_criteria] = $value;
                }
            }
        }
        return $formatted_criteria;
    }

    /**
     * This should return sample counts and total population counts by state
     * With a summation. So the returned object should be:
     * {
     *     "totals": {"sample": 123333, "population": 3434343434343},
     *     "states": {
     *          "Abuja": {"sample": 10, "population": 200000},
     *          "Plateau": {"sample": 5, "population": 30000000}
     *      }
     * }
     */
    protected function getCountsByState() {
        $query = DB::table('mps_audiences')
            ->join('state_populations', 'state_populations.state', '=', 'mps_audiences.state')
            ->select(DB::raw('mps_audiences.state, COUNT(mps_audiences.external_user_id) as sample, state_populations.count as population'))
            ->groupBy('mps_audiences.state')
            ->where('state_populations.year', 2016) //this is the most recent census
            ->get();
        $counts_by_state = array("states" => array());
        $sample_total = 0;
        $population_total = 0;
        foreach($query as $state_results) {
            $state_array = array("sample" => $state_results->sample, "population" => $state_results->population);
            $counts_by_state[$state_results->state] = $state_array;
            $sample_total += $state_results->sample;
            $population_total += $state_results->population;
        }
        return array(
            "totals" => array("sample" => $sample_total, "population" => $population_total),
            "states" => $counts_by_state
        );
    }

    /**
     * This should return a numerical array of sample counts by station and timebelt (which includes the day). i.e
     * [
     *   {"station": "ABC", "day": "Monday", "start_time": "12:00", "end_time": "12:15", "state": "Abuja", "total_audience": 1000000},
     *   {"station": "EFG", "day": "Friday", "start_time": "01:00", "end_time": "01:15", "state": "Kaduna", "total_audience": 5000000}
     * ]
     * @todo Make sure that the values given are all the values. For instance if all ages are selected, then all ages should be in the array
     */
    protected function getTimeBeltsByCriteria() {
        $query = DB::table('mps_audiences as ma')
            ->join('mps_audience_program_activities as mapa', 'mapa.external_user_id', '=', 'ma.external_user_id')
            ->select(DB::raw('mapa.station, mapa.state as station_state, mapa.day, mapa.start_time, mapa.end_time, ma.state, COUNT(DISTINCT ma.external_user_id) as num_respondents'))
            ->groupBy('mapa.station', 'mapa.state', 'mapa.day', 'mapa.start_time', 'mapa.end_time', 'ma.state')
            ->when($this->criteria, function($query){
                foreach($this->criteria as $criteria => $sub_criteria) {
                    $field = "ma." . $criteria;
                    if ($criteria == "age") {
                        foreach($sub_criteria as $age_range) {
                            $query->orWhere(function ($query) use ($age_range, $field) {
                                $query->whereBetween($field, array($age_range['min'], $age_range['max']));
                            });
                        }
                        continue;
                    } 
                    if (is_array($sub_criteria)) {
                        $query->whereIn($field, $sub_criteria);
                        continue;
                    }
                    $query->where($field, $sub_criteria);
                }
            });
            return $query->get();
    }

    /**
     * Loop through the timebelt results, and project onto the state population (this is a naive way to do this)
     * @todo log how long it takes to do this analysis for performance tracking
     * @todo Eventually we will want to return the grouping of timebelts by states (and also allow grouping by program etc)
     */
    protected function analyzeAndFormatResults($timebelt_query, $counts_by_state) {
        /**
         * The results are calculated by states (for statistical accuracy etc)
         * Stations and timebelts that happen to be in different states should now be combined into one
         * But we should still keep a record of the states that were seen as that will be a part of the filtering
         */
        $working_map = collect(array());
        $state_map = collect(array());

        foreach($timebelt_query as $timebelt) {
            $item = $this->projectCounts($timebelt, $counts_by_state);

            //some other to add the grouping of states and stations together
            $state_map[$item["state"]] = true; // we want to end up with a unique list of states

            $audience_count = $item["audience"];
            $key = $item["media_type"] . $item["station"] . $item["day"] . $item["start_time"];

            if (isset($working_map[$key])) {
                $station_rating = $working_map[$key];

                // increment the station rating count
                $station_rating["audience"] += $audience_count;

                // also add this states audience count to the item (for filtering purposes)
                // @todo fix this
                $station_rating["state_counts"][$item["state"]] = $audience_count;
                $item = $station_rating;
            } else {
                $item["state_counts"] = array($item["state"] => $audience_count);
            }

            $working_map[$key] = $item;

        }
        return collect(array(
            "projected_counts" => collect($working_map->values()),
            "state_list" => $state_map->keys()
        ));
    }

    private function projectCounts($timebelt, $counts_by_state) {
        $num_respondents = $timebelt->num_respondents;
        $state_total = $counts_by_state["states"][$timebelt->state]["population"];
        $state_sample = $counts_by_state["states"][$timebelt->state]["sample"];

        //This is a very basic projection
        $projected_audience = round(($num_respondents * $state_total)/$state_sample);
        // $total_count += $projected_audience;

        $program = "Unknown Program";
        $station_name = $this->formatStationName($timebelt);
        $data = array(
            "media_type" => "Tv", //Only tv is supported for now
            "station" => $station_name,
            // "state" => $timebelt->state,
            "state" => $timebelt->station_state,
            "program" => $program,
            "day" => static::DAY_CONVERSION[$timebelt->day],
            "start_time" => $timebelt->start_time,
            "end_time" => $timebelt->end_time,
            "audience" => $projected_audience
        );
        return $data;
    }

    private function formatStationName($timebelt) {
        $station = $timebelt->station;
        return $station;
        // $state = $timebelt->station_state;
        // if ($state != '') {
        //     $station = $station . " (" . $state . ")";
        // }
        // return $station;
    }

    /**
     * Save these to the database
     */
    protected function runInsertQuery($state_list, $projections) {
        // DB::disableQueryLog();
        // app('debugbar')->disable();

        $this->getStationListing();
        $this->getProgramNameMap();

        return Utilities::switch_db('api')->transaction(function() use ($state_list, $projections){
            $new_media_plan = $this->saveMediaPlan($state_list);
            Log::debug("did I save media plan properly?");

            $list_to_save = array();
            // $batch_size = 500;
            $count = 0;
            foreach($projections as $suggestion) {
                $station = $suggestion['station'];
                $state = $suggestion['state'];
                if (isset($this->stationListing[$station]) && isset($this->stationListing[$station][$state])) {
                    $station_type = $this->stationListing[$station][$state]['station_type'];
                    $station_region = $this->stationListing[$station][$state]['region'];
                    $list_to_save[] = [
                        'id' => uniqid(),
                        'media_plan_id' => $new_media_plan->id,
                        'media_type' => $suggestion['media_type'],
                        'station' => $station,
                        'state' => $state,
                        'station_type' => $station_type,
                        'region' => $station_region,
                        'program' => $this->getProgramName($suggestion['station'], $suggestion['day'], $suggestion['start_time'], $suggestion['program']),
                        'day' => $suggestion['day'],
                        'start_time' => $suggestion['start_time'],
                        'end_time' => $suggestion['end_time'],
                        'total_audience' => $suggestion['audience'],
                        'state_counts' => json_encode($suggestion['state_counts']),
                        'created_at' => $new_media_plan->created_at,
                        'updated_at' => $new_media_plan->updated_at,
                    ];
                    $count += 1;
                } else {
                    Log::warn($station . ' or ' . $state . ' is not set');
                }
                // if ($count >= 2000) {
                //     Log::debug("saving batch");
                //     MediaPlanSuggestion::insert($list_to_save);
                //     $list_to_save = [];
                //     $count = 0;
                //     Log::debug("saved batch");
                // }
            }
            
            // if (count($list_to_save) > 0) {
            //     Log::debug("saving last batch");
            //     MediaPlanSuggestion::insert($list_to_save);
            //     Log::debug("saved last batch");
            //     $list_to_save = [];
            //     $count = 0;
            // }
            $media_plan_suggestions = new MediaPlanSuggestion();
            $columns = ['id', 'media_plan_id', 'media_type', 'station', 'state', 'station_type', 'region', 'program', 'day', 'start_time',
                'end_time', 'total_audience', 'state_counts', 'created_at', 'updated_at'
            ];
            $batch_size = 2000;
            $laravel_batch = new LaravelBatch(app('db'));
            $result = $laravel_batch->insert($media_plan_suggestions, $columns, $list_to_save, $batch_size);
            Log::debug($result);
            return $new_media_plan;
        });    
    }

    protected function saveMediaPlan($state_list) {
        $media_plan_data = array(
            'gender' => json_encode($this->criteriaForm->gender),
            'criteria_lsm' => json_encode($this->criteriaForm->lsm),
            'criteria_social_class' => json_encode($this->criteriaForm->social_class),
            'criteria_region' => json_encode($this->criteriaForm->region),
            'criteria_state' => json_encode($this->criteriaForm->state),
            'criteria_age_groups' => json_encode($this->criteriaForm->age_groups),
            'agency_commission' => $this->criteriaForm->agency_commission,
            'start_date' => $this->criteriaForm->start_date,
            'end_date' => $this->criteriaForm->end_date,
            'media_type' => $this->criteriaForm->media_type,
            'campaign_name' => $this->criteriaForm->campaign_name,
            'planner_id' => Auth::id(),
            'status' => 'Suggested',
            'state_list' => json_encode($state_list),
            'filters' => json_encode(array()) //store all the filters which are automatically used to filter the result set
        );
        return MediaPlan::create($media_plan_data);
    }

   
    private function getProgramName($station, $day, $start_time, $default){
        $key = $station . '.' . $day . '.' . $start_time;
        return data_get($this->programNameMap, $key, $default);
    }

    private function getStationListing() {
        $collection = Station::all();
        foreach($collection as $station) {
            $station_name = $station->station;
            if (isset($this->stationListing[$station_name])) {
                $item = $this->stationListing[$station_name];
            } else {
                $item = array();
            }
            $item[$station->state] = array(
                'station_type' => $station->station_type,
                'region' => $station->region
            );
            $this->stationListing[$station_name] = $item;
        } 
    }

    private function getProgramNameMap(){
        $collection = MediaPlanProgram::all();
        foreach($collection as $program_item) {
            $station = $program_item->station;
            $day = $program_item->day;
            $start_time = $program_item->start_time;

            $key = $station . '.' . $day . '.' . $start_time;
            data_set($this->programNameMap, $key, $program_item->program_name);
        }
    }
}