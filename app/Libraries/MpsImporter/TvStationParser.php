<?php

namespace Vanguard\Libraries\MpsImporter;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @todo There are some stations that have not been inputted for one reason or the other
 * i.e NTA International
 */
class TvStationParser
{
        
    const REGEX_LIST = [
        "tv_international" => "/^TV INTERNATIONAL QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)\s\([A-Z|-]+\)_(.*):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/",
        "tv_national_nett" => "/^TV NATIONAL NETTS QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_(.*)(\sNett|\s\(Natl\)\sNett|\s\(Natl Nett\)):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/",
        "tv_national" => "/^TV NATIONAL QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_(\w+)_(.*)(\s\w+|P\/Harcourt|\s[A-Za-z-]+|P\/H):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/",
        // "tv_international_net" => ["satellite", "/TV INTERNATIONAL NETTS QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_(.*)(\sNett|\s\(Intl Nett\)):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/"],
        // "tv_nat_int_net" => ["network", "/^TV_NAT_INT_NETTS_QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_(.*)(\sNett|\s\(Intl Nett\)):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/"]
    ];

    public function __construct()
    {
        $this->raw_station_map = new StationMap();
    }

    public function parse($key) 
    {
        $match_results = $this->match($key);
        
        if ($match_results == null) {
            return null;
        }

        $rgx_key = $match_results[0];
        $matches = $match_results[1];
        $match_groups = count($matches);
        $station_info = [];
        if ($match_groups > 0) {
            $state = '';
            $city = '';
            switch ($match_groups) {
                case 6:
                    $day = $matches[1][0];
                    $station = $matches[2][0];
                    $start_time = $matches[4][0];
                    $end_time = $matches[5][0];
                    break;
                case 7:
                    $day = $matches[1][0];
                    $station = $matches[2][0];
                    $start_time = $matches[5][0];
                    $end_time = $matches[6][0];
                    break;
                case 8: 
                    $day = $matches[1][0];
                    $state = $matches[2][0];
                    $station = $matches[3][0];
                    $city = $matches[4][0];
                    $start_time = $matches[6][0];
                    $end_time = $matches[7][0];
                    break;
                default:
                    # code...
                    break;
            }

            $state = "{$state}";
            $city = "{$city}";

            $day = trim($day);
            $state = trim($state);
            $station = trim($station);
            $city = trim($city);

            $station_info = [
                "name" => Arr::get(static::NAME_MAP, $station, $station), 
                "day" => $day, 
                "timebelt" => "{$start_time}-{$end_time}", 
                "state" => Arr::get(static::STATE_MAP, $state, $state),
                "city" => Arr::get(static::CITY_MAP, $city, $city),
                "station_type" => $station_type
            ];
        }
        return collect($station_info);
    }

    protected function match($station_label) {
        foreach (static::REGEX_LIST as $key => $rgx) {
            $matches = [];
            if (preg_match_all($rgx, $station_label, $matches)) {
                if ($matches[0][0] == $station_label) {
                    return [$key, $matches];
                }
            }
        }
        return null;
    }
}
