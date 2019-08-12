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
            'image_url' => 'sometimes|required|url',
            'street_address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'state' => 'sometimes|required|string',
            'nationality' => 'sometimes|required|string',
            'contact.first_name' => 'sometimes|required|string',
            'contact.last_name' => 'sometimes|required|string',
            'contact.email' => 'sometimes|required|email',
            'contact.phone_number' => 'sometimes|required|string',
            'contact.is_primary' => 'sometimes|required|boolean',
        ];
    }
}
