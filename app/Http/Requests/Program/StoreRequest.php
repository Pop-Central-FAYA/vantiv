<?php

namespace Vanguard\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
                'required',
                'string',
                Rule::unique('media_plan_programs')->where(function($query) {
                    return $query->where('station_id', $this->station_id);
                })
            ],
            'rates' => 'required|array',
            'durations' => 'required|array',
            'days' => 'required|array',
            'start_time' => 'required|array',
            'end_time' => 'required|array'
        ];
    }
}
