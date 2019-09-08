<?php

namespace Vanguard\Libraries\MpsImporter;

use Illuminate\Support\Str;

class TvStationParser
{
    
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
}
