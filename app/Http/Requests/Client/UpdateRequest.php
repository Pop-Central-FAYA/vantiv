<?php

namespace Vanguard\Http\Requests\Client;

use Vanguard\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     * @todo add validations
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
            'contacts.*.first_name' => 'sometimes|required|string',
            'contacts.*.last_name' => 'sometimes|required|string',
            'contacts.*.email' => 'sometimes|required|email',
            'contacts.*.phone_number' => 'sometimes|required|string',
        ];
    }
}
