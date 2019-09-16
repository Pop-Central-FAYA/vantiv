<?php

namespace Vanguard\Http\Requests\MediaPlan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanSuggestionsRequest extends FormRequest
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
     * @todo add more finetuned validation
     * @return array
     */
    public function rules()
    {   
        return [
            'data' => 'required|array',
            'data.*.media_type' => 'required|string',
            'data.*.program' => 'required|string',
            'data.*.day' => 'required|string',
            'data.*.start_time' => 'required|string',
            'data.*.end_time' => 'required|string',
            'data.*.total_audience' => 'required|numeric',
            'data.*.station_id' => 'required|string',
            'data.*.rating' => 'required|numeric',
            'data.*.station' => 'required|string',
            'data.*.state' => 'sometimes|nullable|string',
            'data.*.station_type' => 'required|string'
        ];
    }
}

    