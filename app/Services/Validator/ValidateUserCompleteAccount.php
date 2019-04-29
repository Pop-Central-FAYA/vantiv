<?php

namespace Vanguard\Services\Validator;

class ValidateUserCompleteAccount
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
            ]
        );
    }

    private function rules()
    {
        return [
                'firstname' => 'required',
                'lastname' => 'required',
                'password' => 'required|min:6',
                're_password' => 'required|same:password'
            ];
    }
}
