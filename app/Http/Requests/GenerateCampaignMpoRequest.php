<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateCampaignMpoRequest extends FormRequest
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
            'publisher_id' => 'sometimes:required',
            'ad_vendor_id' => 'sometimes:required',
            'insertions' => 'required',
            'net_total' => 'required',
            'adslots' => 'required|array',
            'group' => 'required'
        ];
    }
}
