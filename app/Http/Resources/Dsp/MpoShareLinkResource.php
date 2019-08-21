<?php

namespace Vanguard\Http\Resources\Dsp;

use Illuminate\Http\Resources\Json\JsonResource;

class MpoShareLinkResource extends JsonResource
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
            'url' => $this->url
        ];
    }
}
