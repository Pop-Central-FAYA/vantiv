<?php

namespace Vanguard\Services\AgencyRating;
use Vanguard\Models\MpsAudience;
use Vanguard\Models\MpsAudienceProgramActivity;
use Vanguard\Models\StatePopulation;
use Illuminate\Support\Collection;

use DB;

/**
 * This class should given a few demographic parameters returns ratings grouped in a certain way
 * @todo log how long it takes to calculate these counts
 */
class GetStationRatings
{

    const SUPPORTED_CRITERIA = array("age", "state", "social_class", "gender", "lsm", "region");

    /**
     * Demographics in this case should be an associative array of criteria such as:
     * {
     *  "age": ["12", "33"] ==> This should be a range
     *  "state": ["Abuja", "Kwara"]
     *  "social_class": ["A", "B", "C"],
     *  "gender": ["Male", "Female"]
     * }
     */
    public function __construct($criteria) {
        $this->criteria = $this->formatCriteria($criteria);
    }

    public function getRatings() {
        $counts_by_state = $this->getCountsByState();
        $timebelt_counts = $this->getTimeBeltsByCriteria();
        $results = $this->analyzeAndFormatResults($timebelt_counts, $counts_by_state);
        return $results;
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
            ->groupBy('mapa.station', 'mapa.state', 'mapa.day', 'mapa.start_time', 'ma.state')
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
        $projected_counts = array();
        $total_count = 0;
        foreach($timebelt_query as $timebelt) {
            $num_respondents = $timebelt->num_respondents;
            $state_total = $counts_by_state["states"][$timebelt->state]["population"];
            $state_sample = $counts_by_state["states"][$timebelt->state]["sample"];

            //This is a very basic projection
            $projected_audience = round(($num_respondents * $state_total)/$state_sample);
            $total_count += $projected_audience;

            $program = "Unknown Program";
            $station_name = $this->formatStationName($timebelt);
            $projected_counts[] = array(
                "media_type" => "Tv", //Only tv is supported for now
                "station" => $station_name,
                "state" => $timebelt->state,
                "program" => $program,
                "day" => $timebelt->day,
                "start_time" => $timebelt->start_time,
                "end_time" => $timebelt->end_time,
                "audience" => $projected_audience
            );
        }
        // Sum the results by state (projection is done by state, since each states population is different
        // but timebelt is the summation of all the counts across states)


        // Sort by Total Audience Descending
        $collection = collect($projected_counts);

        return array(
            'total_tv' => $total_count,
            'programs_stations' => $collection->sortByDesc('total_audience')->toArray(),
            'stations' => $collection->groupBy('station')->toArray(),
            'total_audiences' => $total_count
        );
    }

    private function formatStationName($timebelt) {
        $station = $timebelt->station;
        $state = $timebelt->station_state;
        if ($state != '') {
            $station = $station . " (" . $state . ")";
        }
        return $station;
    }

}