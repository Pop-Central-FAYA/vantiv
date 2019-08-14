<?php

namespace Vanguard\Http\Requests\Brand;

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
            'client_id' => 'required|string',
            'name' => 'required|string',
            'image_url' => 'required|url',
        ];
    }
}