<?php

namespace Vanguard\Http\Requests\Station;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Used to validate the parameters used to filter a list of ad vendors
 */
class ListRequest extends FormRequest
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

    public function rules()
    {
        return [
            'publisher' => 'sometimes|required|string', 
        ];
    }
}
