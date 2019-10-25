<?php

namespace Vanguard\Http\Requests\Station;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'required', 
                'string', 
                Rule::unique('tv_stations')->where(function($query) {
                    return $query->where('state', $this->request->all()['state'])
                                ->where('type', $this->request->all()['type']);
                })->ignore($this->id)
            ],
            'type' => 'sometimes|required|string',
            'state' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'region' => 'sometimes|required|string',
            'broadcast' => 'sometimes|required|string',
        ];
    }
}
