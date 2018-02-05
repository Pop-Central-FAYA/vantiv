<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdslotsRequests extends FormRequest
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
            'from_time' => 'required',
            'to_time' => 'required',
            'price_60' => 'required',
            'price_45' => 'required',
            'price_30' => 'required',
            'price_15' => 'required',
            'region' => 'required',
            'target_audience' => 'required',
            'dayparts' => 'required',
            'min_age' => 'required',
            'max_age' => 'required',
        ];
    }
}
