<?php

namespace Vanguard\Http\Requests\User;

use Vanguard\Http\Requests\Request;

class UpdateUserRequest extends Request
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
            'role_name' => 'required|array',
            'status' => 'required|String|in:Active,Inactive,Unconfirmed'
        ];
    }
}


