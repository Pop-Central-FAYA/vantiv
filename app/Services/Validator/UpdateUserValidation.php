<?php

namespace Vanguard\Services\Validator;

class UpdateUserValidation
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function validateRequest()
    {
        return $this->validatorRules($this->request);
    }

    private function validatorRules(array $data)
    {
        $rules = $this->rules();
        return \Validator::make($data, $rules,
            [
                'required' => ':attribute is required',
                'array' => ':attribute cannot be string'
            ]
        );
    }

    private function rules()
    {
        $rules_array =  [
            'roles' => 'required|array',
        ];
        if(isset($this->request['companies'])) {
            array_push($rules_array, ['companies' => 'required|array']);
        }
        return $rules_array;
    }
}
