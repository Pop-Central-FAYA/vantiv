<?php

namespace Vanguard\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address' => 'sometimes|required|string',
            'logo' => 'sometimes|required|url',
            'city' => 'sometimes|required|string',
            'state' => 'sometimes|required|string',
            'country' => 'sometimes|required|string',
            'company_rc' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'phone_number' => 'sometimes|required|string',
            'color' => 'sometimes|required|string',
            'website' => 'sometimes|required|string',

        ];
    }
}