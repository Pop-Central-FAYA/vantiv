<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaPlanProgramResource extends JsonResource
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
            'name' => $this->program_name,
            'attributes' => json_decode($this->attributes, true),
            'ad_vendors' => $this->ad_vendors->map(function ($model) {
                return ['id' => $model->id, 'name' => $model->name];
            }),
            'links' => [
                'update' => route('programs.update', ['station_id' => $this->station_id, 'program_id' => $this->id], false)
            ] 
        ];
    }
}
