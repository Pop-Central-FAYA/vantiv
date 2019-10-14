<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TvStationRatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "name" => $this['station'],
            "id" => $this['station_id'],
            "key" => $this['station_key'],
            "type" => $this['station_type'],
            "state" => $this['station_state'],
            "total_audience" => $this['total_audience'],
            "links" => [
                'timebelt_ratings' => route('reach.get-timebelts', ['plan_id' => $request->plan_id], false)
            ]
        ];
    }
}
