<?php

namespace Vanguard\Libraries;

class TimeBelt
{   

    public static function getTimebeltKey($item) {
        return md5("{$item['station_id']}-{$item['day']}-{$item['start_time']}");
    }
}
