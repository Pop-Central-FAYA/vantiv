<?php

namespace Vanguard\Services\MediaPlan;
use Validator;

class validateCriteriaForm
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function validateCriteria()
    {
        // validate request
        return $this->validationRules($this->request);
    }

    /**
     * Get a validator for an incoming criteria request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validationRules(array $data)
    {
        $rules = [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:today',
            'region' => 'nullable|array',
            'state' => 'nullable|array',
            'gender' => 'nullable|string',
            'lsm' => 'nullable|array',
            'social_class' => 'nullable|array',
            'age_groups' => 'nullable|array',
            'agency_commission' => 'nullable|numeric',
            'media_type' => 'required|string'
        ];
        return Validator::make($data, $rules,
            [
                'required' => ':attribute is required',
                'unique' => ':attribute already exists',
            ]
        );
    }
}
