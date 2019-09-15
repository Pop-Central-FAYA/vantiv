<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Vanguard\Models\TvStation;

class TvStationTimeBeltRatingGraphResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $grouped = $this->groupBy('tv_station_key');
        return $this->formatResponseWithStationData($grouped);
    }

    protected function formatResponseWithStationData($grouped)
    {
        $tv_station_map = TvStation::all()->groupBy('key');
        foreach ($grouped as $station_key => $timebelts) {
            $tv_station = Arr::get($tv_station_map, $station_key);
            if ($tv_station) {
                $tv_station = $tv_station[0];
                $station_name = $tv_station['name'];
                $state = trim($tv_station['state']);
                if ($state) {
                    $station_name = "{$station_name} ({$state})";
                }
            } else {
                //could not get the tv station
                Log::warning("Could not get the tv station for {$station_key} while graphing");
                $station_name = $station_key;
            }
            $data[$station_name] = $timebelts;
        }
        return $data;
    }
}

