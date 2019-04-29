<?php

namespace Vanguard\Http\Requests\Publisher;

use Illuminate\Foundation\Http\FormRequest;

class DashboardReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @todo Here check that the user has the appropriate role
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
    public function rules() {
        return [
            'media_type' => 'required|string',
            'report_type' => 'required|string',
            'station_id' => 'nullable|array',
            'year' => 'required|string',

        ];
    }
}