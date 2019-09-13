<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'ad_vendor_id' => 'required',
            'insertions' => 'required',
            'net_total' => ['required',
                            Rule::unique('campaign_mpos')->where(function($query) {
                                return $query->where('campaign_id', $this->campaign_id)
                                            ->where('ad_vendor_id', $this->ad_vendor_id);
                            })->ignore($this->id)
                            ],
            'adslots' => 'required|array'
        ];
    }

    /**
     * Get custom message for unique net_total
     */
    public function messages()
    {
        return [
            'net_total.unique' => 'You already generated an mpo, please make changes before generating another one',
        ];
    }
}
