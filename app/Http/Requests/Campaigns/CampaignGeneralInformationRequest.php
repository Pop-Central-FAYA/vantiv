<?php

namespace Vanguard\Http\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;

class CampaignGeneralInformationRequest extends FormRequest
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
        if(\Session::get('broadcaster_id')){
            return [
                'client' => 'required',
                'campaign_name' => 'required',
                'brand' => 'required',
                'product' => 'required',
                'min_age' => 'required',
                'max_age' => 'required',
                'target_audience' => 'required_without_all',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'industry' => 'required',
                'dayparts' => 'required_without_all',
                'region' => 'required_without_all'
            ];
        }else{
            return [
                'client' => 'required',
                'campaign_name' => 'required',
                'brand' => 'required',
                'product' => 'required',
                'channel' => 'required_without_all',
                'min_age' => 'required',
                'max_age' => 'required',
                'target_audience' => 'required_without_all',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'industry' => 'required',
                'dayparts' => 'required_without_all',
                'region' => 'required_without_all',
            ];
        }
    }
}
