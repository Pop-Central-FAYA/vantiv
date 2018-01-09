<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClient extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required | email | unique:users,email',
            'username' => 'unique:users,username',
            'password' => 'required | min:6 | confirmed',
            'birthday' => 'date',
            'role' => 'required | exists:roles,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'country_id' => 'required',
            'location' => 'required',
            'image_url' => 'required'
        ];
    }
}
