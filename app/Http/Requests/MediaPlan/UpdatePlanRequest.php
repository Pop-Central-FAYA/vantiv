<?php

namespace Vanguard\Http\Requests\MediaPlan;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {   
        return [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:today',
            'campaign_name' => 'required|string',
            'product' => 'required|string',
            'client' => 'required|string',
            'brand' => 'required|string',
            'agency_commission' => 'nullable|numeric',
        ];
    }
}