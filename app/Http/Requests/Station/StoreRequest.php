<?php

namespace Vanguard\Http\Requests\Station;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'publisher_id' => 'required|string|exists:publishers,id',
            'name' => [
                'required',
                'string',
                Rule::unique('tv_stations')->where(function($query) {
                    return $query->where('type', $this->request->all()['type'])
                                ->where('state', $this->request->all()['state']);
                })
            ],
            'type' => 'required',
            'state' => 'required',
            'city' => 'required',
            'region' => 'required',
            'key' => 'required',
            'broadcast' => 'required',
        ];
    }
}
