<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Arr;

class TimeBelt
{   

    const LENGTHEN_DAY_MAP = array(
        "Mon" => "Monday",
        "Tue" => "Tuesday",
        "Wed" => "Wednesday",
        "Thu" => "Thursday",
        "Fri" => "Friday",
        "Sat" => "Saturday",
        "Sun" => "Sunday"
    );

    const SHORTEN_DAY_MAP = array(
        "Monday" => "Mon",
        "Tuesday" => "Tue",
        "Wednesday" => "Wed",
        "Thursday" => "Thu",
        "Friday" => "Fri",
        "Saturday" => "Sat",
        "Sunday" => "Sun"
    );

    public static function getTimebeltKey($item) {
        $day = static::shortenDay($item['day']);
        return md5("{$item['station_key']}-{$day}-{$item['start_time']}");
    }

    public static function shortenDay($day) {
        return Arr::get(static::SHORTEN_DAY_MAP, $day, $day);
    }

    public static function lengthenDay($day) {
        return Arr::get(static::LENGTHEN_DAY_MAP, $day, $day);
    }
}
