<?php

namespace Vanguard\Libraries\Station;

use Symfony\Component\Yaml\Yaml;

class Reader
{   

    public static function getTvStationList() 
    {
        $filepath = static::getYmlFilePath();
        return Yaml::parseFile($filepath);
    }

    public static function getYmlFilePath()
    {
        return dirname(__FILE__) . "/tv-stations.yml";
    }
}
