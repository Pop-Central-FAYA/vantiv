<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vanguard\Models\TvStation;

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
        $tv_station = TvStation::where('key', $this['tv_station_key']);
        return [
            'key' => $this['key'],
            "program" => $this['program'],
            "day" => $this['day'],
            "start_time" => $this['start_time'],
            "end_time" => $this['end_time'],
            "total_audience" => $this['total_audience'],
            "rating" => $this['rating'],
            "station_key" => $request->station_key,
            "tv_station_key" => $this['tv_station_key'],
            "media_type" => $this['media_type'],
            "station_id" => $this['station_id'],
            "station" => $this['station'],
            "state" => $this['state'],
            "station_type" => $this['station_type']
        ];
    }
}