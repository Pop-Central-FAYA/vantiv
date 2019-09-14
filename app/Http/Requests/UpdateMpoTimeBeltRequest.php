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
            'id' => 'required',
            'program' => 'required|sometimes',
            'playout_date' => 'required|sometimes',
            'unit_rate' => 'required|sometimes',
            'asset_id' => 'required|sometimes',
            'time_belt_start_time' => 'required|sometimes',
            'ad_slots' => 'required|sometimes',
            'volume_discount' => 'required',
            'day' => 'required|sometimes',
            'ad_vendor_id' => 'nullable',
        ];
    }
}
