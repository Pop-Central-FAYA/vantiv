<?php

namespace Vanguard\Http\Requests\Profile;

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
            'firstname' => 'sometimes|required|string',
            'lastname' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'phone_number' => 'sometimes|required|string',
            'avatar' => 'sometimes|required|url',
        ];
    }
}
