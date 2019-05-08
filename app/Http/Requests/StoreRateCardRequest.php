<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vanguard\Rules\RateCard\RateCardRule;

class StoreRateCardRequest extends FormRequest
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
            'title' => ['required', new RateCardRule($this->title, $this->company)],
            'is_base' => 'boolean',
            'duration' => 'array|required',
            'price' => 'array|required'
        ];
    }
}
