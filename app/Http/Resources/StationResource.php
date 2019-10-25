<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
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
            'id' => $this->id,
            'publisher'=> $this->publisher,
            'name' => $this->name,
            'type' => $this->type,
            'broadcast' => $this->broadcast,
            'programs' => MediaPlanProgramResource::collection($this->programs),
            'links' => [
                'details' => route('stations.details', ['id' => $this->id], false),
                'update' => route('stations.update', ['id' => $this->id], false),
                'store_program' => route('programs.store', ['id' => $this->id], false)
            ]
        ];
    }
}
