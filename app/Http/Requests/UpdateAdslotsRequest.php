<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdslotsRequest extends FormRequest
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
            'time_60' => 'required',
            'time_45' => 'required',
            'time_30' => 'required',
            'time_15' => 'required'
        ];
    }
}
