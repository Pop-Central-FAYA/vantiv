<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'id' =>  $this->id,
            'name' =>  $this->name,
            'image_url' =>  $this->image_url,
            'created_by' => $this->created_by,
            'client_id' => $this->client_id,
            'created_at' => $this->created_at,
            'campaigns_count' => $this->campaigns->count(),
            'campaigns_spendings' => $this->campaigns->sum('budget'),
        ];
    }
}