<?php

namespace Vanguard\Libraries\MpsImporter;

use Mpdf\Tag\Dd;
use Symfony\Component\Yaml\Yaml;

class StationMap
{
        
    public function __construct()
    {
        $national_map = $this->parseYaml('tv-national.yml');
        $network_map = $this->parseYaml('tv-national-network.yml');
        $international_map = $this->parseYaml('tv-international.yml');

        $this->station_map = $national_map->merge($network_map)->merge($international_map);
    }

    public function get($key)
    {
        return $this->station_map->get($key, null);
    }

    protected function parseYaml($file_name)
    {
        $res = Yaml::parseFile($this->getFilePath($file_name));
        return collect($res)->groupBy('raw_name');
    }

    protected function getFilePath($file_name) 
    {
        return dirname(__FILE__) . "/Maps/{$file_name}";
    }
}
