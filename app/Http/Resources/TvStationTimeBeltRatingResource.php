<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class TvStationTimeBeltRatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $full_name = $this['station'];
        $state = $this['state'];
        if ($state != '') {
            $full_name = "{$full_name} ({$state})";
        }
        return [
            'key' => $this['key'],
            "program" => Arr::get($this, "program", "Unknown Program"),
            "day" => $this['day'],
            "start_time" => $this['start_time'],
            "end_time" => $this['end_time'],
            "total_audience" => $this['total_audience'],
            "rating" => $this['rating'],
            "station_key" => $this['station_key'],
            "tv_station_key" => $this['station_key'],
            "media_type" => $this['media_type'],
            "station_id" => $this['station_id'],
            "station" => $this['station'],
            "state" => $state,
            "station_type" => $this['station_type'],
            "station_name" => $full_name,
        ];
    }
}