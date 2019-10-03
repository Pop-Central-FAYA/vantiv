<?php

namespace Vanguard\Http\Requests\Profile;

use Vanguard\Http\Requests\Request;

class PasswordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     * @todo add validations
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|string',
        ];
    }
}
