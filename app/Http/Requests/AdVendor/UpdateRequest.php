<?php

namespace Vanguard\Http\Requests\AdVendor;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

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
     * @todo add phone number validation
     * @todo add country validation
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'sometimes',
                'required', 
                'string', 
                Rule::unique('ad_vendors')->where(function($query) {
                    return $query->whereIn('company_id', $this->user()->companyIdList());
                })->ignore($this->id)
            ],
            'street_address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'state' => 'sometimes|required|string',
            'country' => 'sometimes|required|string',
            'contacts.*.first_name' => 'sometimes|required|string',
            'contacts.*.last_name' => 'sometimes|required|string',
            'contacts.*.email' => 'sometimes|required|email',
            'contacts.*.phone_number' => 'sometimes|required|string',
            'publishers' => 'sometimes|array',
            'publishers.*.id' => 'sometimes|string|exists:publishers,id'
        ];
    }
}