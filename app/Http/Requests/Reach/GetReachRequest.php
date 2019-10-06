<?php

namespace Vanguard\Http\Requests\Reach;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Vanguard\Libraries\DayPartList;
use Vanguard\Models\Criteria;

class GetReachRequest extends FormRequest
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
     * @todo refactor this listing (The way we get valid states for instance etc)
     */
    public function rules()
    {
        return [
            'day_part' => [
                'required',
                'string',
                Rule::in($this->getDayParts())
            ],
            'state' => [
                'required',
                'string',
                Rule::in($this->getStateList())
            ],
            'day' => [
                'required',
                'string',
                Rule::in(["all", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"])
            ],
            'station_type' => [
                'required',
                'string',
                Rule::in(["all", "network", "regional", "international"])
            ],
        ];
    }

    protected function getStateList()
    {
        $model = Criteria::with('subCriterias')->where('name', 'states')->first();
        $state_list = $model->subCriterias->sortBy('name')->pluck('name');
        $state_list[] = 'all';
        return $state_list;
    }

    protected function getDayParts()
    {
        $day_parts = array_keys(DayPartList::DAYPARTS);
        $day_parts[] = 'all';
        return $day_parts;
    }
}