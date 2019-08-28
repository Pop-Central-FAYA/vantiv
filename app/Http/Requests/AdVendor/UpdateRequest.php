<?php

namespace Vanguard\Http\Requests\AdVendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends StoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @todo add phone number validation
     * @todo add country validation
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required|string',
            'street_address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'state' => 'sometimes|required|string',
            'country' => 'sometimes|required|string',
            'contacts.*.first_name' => 'sometimes|required|string',
            'contacts.*.last_name' => 'sometimes|required|string',
            'contacts.*.email' => 'sometimes|required|email',
            'contacts.*.phone_number' => 'sometimes|required|string',
            'publishers' => 'sometimes|array',
            'publishers.*' => 'string|exists:publishers,id'
        ];
    }
}