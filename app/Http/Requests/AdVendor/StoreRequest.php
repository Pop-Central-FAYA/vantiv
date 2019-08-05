<?php

namespace Vanguard\Http\Requests\AdVendor;

use Illuminate\Foundation\Http\FormRequest;

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
     * @todo add phone number validation
     * @todo add country validation
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'contacts.*.first_name' => 'required|string',
            'contacts.*.last_name' => 'required|string',
            'contacts.*.email' => 'required|email',
            'contacts.*.phone_number' => 'required|string'
        ];
    }
}
