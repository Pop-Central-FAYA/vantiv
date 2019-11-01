<?php

namespace Vanguard\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'program_name' => [
                    'sometimes',
                    'required', 
                    'string', 
                    Rule::unique('media_plan_programs')->where(function($query) {
                        return $query->where('station_id', $this->station_id);
                    })->ignore($this->program_id)
                ],
            'rates' => 'sometimes|required|array',
            'days' => 'sometimes|required|array',
            'durations' => 'sometimes|required|array',
            'start_time' => 'sometimes|required|array',
            'end_time' => 'sometimes|required|array',
            'ad_vendors' => 'sometimes|array',
            'ad_vendors.*.id' => 'sometimes|string|exists:ad_vendors,id'
        ];
    }
}
