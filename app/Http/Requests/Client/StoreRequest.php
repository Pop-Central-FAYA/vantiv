<?php

namespace Vanguard\Http\Requests\Client;

use Vanguard\Http\Requests\Request;

class StoreRequest extends Request
{



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
            'name' => 'required|string',
            'image_url' => 'required|url',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'nationality' => 'required|string',
            'contacts.*.first_name' => 'required|string',
            'contacts.*.last_name' => 'required|string',
            'contacts.*.email' => 'required|email',
            'contacts.*.phone_number' => 'required|string',
            'contacts.*.is_primary' => 'required|boolean',
            'brands.*.name' => 'required|string',
            'brands.*.image_url' => 'required|url',
        ];
    }
}