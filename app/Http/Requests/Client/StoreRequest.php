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
            'name' => 'required',
            'image_url' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'nationality' => 'required',
            'client_contact.first_name' => 'required',
            'client_contact.last_name' => 'required',
            'client_contact.email' => 'required',
            'client_contact.phone_number' => 'required',
            'client_contact.is_primary' => 'required',
            'brand_details.name' => 'required',
            'brand_details.image_url' => 'required',
            'brand_details.status' => 'required'
        ];
    }
}