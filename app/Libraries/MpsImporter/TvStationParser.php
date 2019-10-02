<?php

namespace Vanguard\Libraries\MpsImporter;

use Log;

class TvStationParser
{
        
    const RGX = "/^(TV INTERNATIONAL QTR|TV NATIONAL QTR|TV NATIONAL NETTS QTR)_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)(\s|_)(.*):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/";

    public function __construct()
    {
        $this->raw_station_map = new StationMap();
    }

    public function parse($key) 
    {
        $matches = $this->match($key);
        if ($matches === null) {
            return null;
        }

        $raw_name = $matches["raw_name"];
        $data = $this->raw_station_map->get($raw_name);
        if ($data === null) {
            Log::warning("Station {$key} with {$raw_name} not found in mapping");
            return null;
        }
        $activity_info = [
            "name" => $data["name"],
            "day" => $matches["day"], 
            "start_time" => $matches["start_time"],
            "end_time" => $matches["end_time"],
            "state" => $data["state"],
            "city" => $data["city"],
            "broadcast_type" => $data["broadcast_type"]
        ];
        return collect($activity_info);
    }

    protected function match($station_label) {
        $matches = [];
        if (preg_match_all(static::RGX, $station_label, $matches)) {
            if ($matches[0][0] == $station_label) {
                return [
                    "day" => $matches[2][0],
                    "raw_name" => $matches[4][0],
                    "start_time" => $matches[6][0],
                    "end_time" => $matches[7][0]
                ];
            }
        }
        return null;
    }
}
