<?php

namespace Vanguard\Http\Requests\User;

use Vanguard\Http\Requests\Request;

class UserInviteRequest extends Request
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
            'roles' => 'required|array',
            'email' => 'required|email|unique:users'
        ];
    }
}