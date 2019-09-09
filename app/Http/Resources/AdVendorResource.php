<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vanguard\Models\Publisher;

class AdVendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @todo only render contacts on eager loading
     * @todo once we have an spa, let the links lead to actual apis to update, delete resources etc
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['contacts'] = AdVendorContactResource::collection($this->contacts);
        $data['publishers'] = $this->publishers->map(function ($model) {
            return ['id' => $model->id, 'name' => $model->name];
        });
        $data['links'] = [
            'self' => route('ad-vendor.get', ['id' => $this->id], false),
            'update' => route('ad-vendor.update', ['id' => $this->id], false),
            'details' => route('ad-vendor.details', ['id' => $this->id], false),
        ];
        return $data;
    }
}
