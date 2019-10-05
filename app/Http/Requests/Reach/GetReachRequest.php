<?php

namespace Vanguard\Http\Requests\Reach;

use Illuminate\Foundation\Http\FormRequest;

class GetReachRequest extends FormRequest
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
        return [
            'media_plan_id' => '',
            'day_part' => '',
            'state' => '',
            'day' => '',
            'station_type' => ''
        ];
    }
}