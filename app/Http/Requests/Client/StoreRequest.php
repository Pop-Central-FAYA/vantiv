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
            'contact.first_name' => 'required|string',
            'contact.last_name' => 'required|string',
            'contact.email' => 'required|email',
            'contact.phone_number' => 'required|string',
            'contact.is_primary' => 'required|boolean',
            'brand_details.name' => 'required|string',
            'brand_details.image_url' => 'required|url',
        ];
    }
}