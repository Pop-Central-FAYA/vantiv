<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMpoTimeBeltRequest extends FormRequest
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
            'program' => 'required',
            'playout_date' => 'required',
            'unit_rate' => 'required',
            'asset_id' => 'required',
            'time_belt' => 'required',
            'insertion' => 'required',
            'volume_discount' => 'required'
        ];
    }
}
