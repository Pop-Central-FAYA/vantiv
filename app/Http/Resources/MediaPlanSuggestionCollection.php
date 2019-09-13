<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MediaPlanSuggestionCollection extends ResourceCollection
{   

    public $collects = 'Vanguard\Http\Resources\MediaPlanSuggestionResource';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
