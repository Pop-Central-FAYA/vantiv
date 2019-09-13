<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vanguard\Libraries\TimeBelt;

class MediaPlanSuggestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['key'] = TimeBelt::getTimebeltKey($this);
        return $data;
    }
}
