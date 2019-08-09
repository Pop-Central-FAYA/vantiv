<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        $data['links'] = [
            'self' => route('ad-vendor.get', ['id' => $this->id], false),
            'update' => route('ad-vendor.update', ['id' => $this->id], false),
        ];
        return $data;
    }
}
