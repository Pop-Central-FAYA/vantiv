<?php

namespace Vanguard\Http\Requests\Brand;

use Vanguard\Http\Requests\Request;

class UpdateRequest extends Request
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
            'name' => 'sometimes|required|string',
            'image_url' => 'sometimes|required|url',
        ];
    }
}