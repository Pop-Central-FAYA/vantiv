<?php

namespace Vanguard\Http\Requests\Mpo;

use Illuminate\Foundation\Http\FormRequest;

class AcceptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'mpo_id' => 'unique:mpo_accepters,mpo_id'
        ];
    }

    public function messages()
    {
        return [
            'mpo_id.unique' => 'This mpo has already been accepted'
        ];
    }
}
