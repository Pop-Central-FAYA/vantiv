<?php

namespace Vanguard\Http\Requests\Reach;

class GetStationTimebeltRequest extends GetReachRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules['station_key'] = '';
        return $rules;
    }
}