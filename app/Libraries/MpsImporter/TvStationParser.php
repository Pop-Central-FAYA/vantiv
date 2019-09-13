<?php

namespace Vanguard\Libraries\MpsImporter;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TvStationParser
{
        
    const REGEX_LIST = [
        "tv_international" => ["satellite", "/^TV INTERNATIONAL QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)\s\([A-Z|-]+\)_(.*):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/"],
        "tv_national_nett" => ["network", "/^TV NATIONAL NETTS QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_(.*)(\sNett|\s\(Natl\)\sNett|\s\(Natl Nett\)):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/"],
        "tv_national" => ["regional", "/^TV NATIONAL QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_(\w+)_(.*)(\s\w+|P\/Harcourt|\s[A-Za-z-]+|P\/H):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/"],
        "tv_international_net" => ["satellite", "/TV INTERNATIONAL NETTS QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_(.*)(\sNett|\s\(Intl Nett\)):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/"],
        "tv_nat_int_net" => ["network", "/^TV_NAT_INT_NETTS_QTR_(Mon|Tue|Wed|Thu|Fri|Sat|Sun)_(.*)(\sNett|\s\(Intl Nett\)):(Mon|Tue|Wed|Thu|Fri|Sat|Sun):(\d{2}h\d{2})-(\d{2}h\d{2})$/"]
    ];

    const STATE_MAP = [
        "CrossRiver" => "Cross River",
        "Ilorin" => "Kwara"
    ];

    const CITY_MAP = [
        "Harcourt" => "Port Harcourt",
        "P/Harcourt" => "Port Harcourt",
        "P/H" => "Port Harcourt",
        "5" => "Lagos",
        "10" => "Lagos",
        "TV" => "",
        "Television" => "",
        "Screen" => "",
        "Continental" => "",
        "Edo" => "",
        "Oyo" => "Ibadan"
    ];

    const NAME_MAP = [
        "ABS TV" => "ABS",
        "AIT Port" => "AIT",
        "CTV Channel 67" => "CTV",
        "EBS CH 55" => "EBS",
        "Ekiti" => "Ekiti State Televison",
        "BCOS CH 28" => "BCOS",
        "Silverbird Television" => "Silverbird TV",
        "Silverbird" => "Silverbird TV",
        "Silverbird TV CH 31" => "Silverbird TV",
        "RSTV Port" => "RSTV",
        "On TV" => "ONTV",
        "NTA Port" => "NTA",
        // "NTA International" => "NTA",
        "NTA Channel 35" => "NTA",
        "NTA CH 45 & 7" => "NTA",
        "NTA 2CH" => "NTA 2",
        "NTA 2  CH" => "NTA 2",
        "NTA CH" => "NTA",
        "NSTV (NTA Station)" => "NSTV",
        "Kwese Free Sports (KFS) UHF 32" => "Kwese Free Sports",
        "Sokoto State" => "Sokoto State Television",
        "Super" => "Super Screen",
        "TV" => "Lagos Continental"

    ];

    public static function parse($key) 
    {
        $station_info = [];
        if (Str::startsWith($key, 'TV_NAT_INT_NETTS_QTR_')) {
            // TV_NAT_INT_NETTS_QTR_Mon_NTA Nett:Mon:07h15-07h30=1
            $slice = Str::after($key, 'TV_NAT_INT_NETTS_QTR_');
            $slice = Str::after($slice, '_');
            $items = explode(":", $slice);
            $timebelt = $items[2];
            $day = $items[1];
            $station = trim(Str::before($items[0], "Nett"));

            $station_info = [
                "name" => $station, 
                "day" => $day, 
                "timebelt" => $timebelt, 
                "state" => "", 
                "city" => "",
                "station_type" => "network"
            ];
        }

        if (Str::startsWith($key, 'TV INTERNATIONAL QTR_')) {
            // TV INTERNATIONAL QTR_Mon (S-Z)_Zee World:Mon:21h00-21h15=1
            $slice = Str::after($key, 'TV INTERNATIONAL QTR_');
            $slice = Str::after($slice, ' ');
            $items = explode(":", $slice);
            $timebelt = $items[2];
            $day = $items[1];

            $station = Str::after($items[0], "(A-C)_");
            $station = Str::after($station, "(M)_");
            $station = Str::after($station, "(N-R)_");
            $station = Str::after($station, "(S-Z)_");
            
            $station = trim($station);

            $station_info = [
                "name" => $station, 
                "day" => $day, 
                "timebelt" => $timebelt, 
                "state" => "", 
                "city" => "",
                "station_type" => "satellite"
            ];
        }

        if (Str::startsWith($key, 'TV NATIONAL NETTS QTR_')) {
            // TV NATIONAL NETTS QTR_Mon_NTA (Natl Nett):Mon:07h15-07h30=1
            $slice = Str::after($key, 'TV NATIONAL NETTS QTR_');
            $slice = Str::after($slice, '_');
            $items = explode(":", $slice);
            $timebelt = $items[2];
            $day = $items[1];

            $station = Str::before($items[0], "(Natl Nett)");
            $station = trim($station);

            $station_info = [
                "name" => $station, 
                "day" => $day, 
                "timebelt" => $timebelt, 
                "state" => "", 
                "city" => "",
                "station_type" => "network"
            ];
        }

        if (Str::startsWith($key, 'TV NATIONAL QTR_')) {
            // TV NATIONAL QTR_Mon_Abia_NTA Aba:Mon:07h15-07h30=1
            $slice = Str::after($key, 'TV NATIONAL QTR_');
            $slice = Str::after($slice, '_');
            $items = explode(":", $slice);

            $timebelt = $items[2];
            $day = $items[1];

            $items = explode("_", $items[0]);
            $state = $items[0];

            $city_station = $items[1];
            $last_space_idx = strripos($city_station, ' ');
            $city = trim(substr($city_station, $last_space_idx));

            $station = trim(Str::before($city_station, $city));

            $station_info = [
                "name" => $station, 
                "day" => $day, 
                "timebelt" => $timebelt, 
                "state" => $state, 
                "city" => $city,
                "station_type" => "regional"
            ];
        }
       
        if (Str::startsWith($key, 'TV INTERNATIONAL NETTS QTR_')) {
            // TV INTERNATIONAL NETTS QTR_Tue_AMC Nett:Tue:20h00-20h15
            $slice = Str::after($key, 'TV INTERNATIONAL NETTS QTR_');
            $slice = Str::after($slice, '_');
            $items = explode(":", $slice);
            $timebelt = $items[2];
            $day = $items[1];

            $station = Str::before($items[0], "(Intl Nett)");
            $station = Str::before($station, "Nett");
            $station = trim($station);

            $station_info = [
                "name" => $station, 
                "day" => $day, 
                "timebelt" => $timebelt, 
                "state" => "", 
                "city" => "",
                "station_type" => "satellite"
            ];
        }
        return collect($station_info);
    }

    public static function parseRgx($key) 
    {
        $match_results = static::match($key);
        $station_type = $match_results[0];
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

    public static function match($station_label) {
        foreach (static::REGEX_LIST as $key => $rgx_list) {
            $matches = [];
            $station_type = $rgx_list[0];
            $rgx = $rgx_list[1];
            if (preg_match_all($rgx, $station_label, $matches)) {
                if ($matches[0][0] == $station_label) {
                    return [$station_type, $matches];
                }
            }
        }
        return ['', []];
    }
}
