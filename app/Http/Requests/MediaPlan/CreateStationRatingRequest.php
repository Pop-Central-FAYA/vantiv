<?php

namespace Vanguard\Http\Requests\MediaPlan;

use Illuminate\Foundation\Http\FormRequest;

class CreateStationRatingRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     * @todo Validate that the different values are within the allowed value list, i.e states actually exist etc
     * @return array
     */
    public function rules()
    {   
        return [
            'station_type' => 'sometimes|required|string',
            'day' => 'sometimes|required|string',
            'state' => 'sometimes|required|string',
            'day_part' => 'sometimes|required|string'
        ];
    }
}