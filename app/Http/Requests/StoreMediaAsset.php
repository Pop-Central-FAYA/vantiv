<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaAsset extends FormRequest
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
            'client_id' => 'required|string',
            'brand_id' => 'required|string',
            'media_type' => 'required|string',
            'asset_url' => 'required|string',
            'regulatory_cert_url' => 'nullable|string',
            'file_name' => 'required|string',
            'duration' => 'required|integer'
        ];
    }
}
