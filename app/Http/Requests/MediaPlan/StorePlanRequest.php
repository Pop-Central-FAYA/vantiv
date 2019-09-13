<?php

namespace Vanguard\Http\Requests\MediaPlan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
            'region' => 'nullable|array',
            'state' => 'nullable|array',
            'gender' => 'nullable|array',
            'social_class' => 'nullable|array',
            'age_groups' => 'nullable|array',
            'agency_commission' => 'nullable|numeric',
            'media_type' => 'required|string',
            'campaign_name' => 'required|string',
        ];
    }
}