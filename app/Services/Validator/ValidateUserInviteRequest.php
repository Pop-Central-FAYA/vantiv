<?php

namespace Vanguard\Services\Validator;

class ValidateUserInviteRequest
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function validateRequest()
    {
        foreach ($this->request['email'] as $email) {
            $request_array = ['roles' => $this->request['roles'], 'email' => $email];
            return $this->validatorRules($request_array);
        }
    }

    private function validatorRules(array $data)
    {
        $rules = $this->rules();
        return \Validator::make($data, $rules,
            [
                'required' => ':attribute is required',
                'email' => $data['email'].' is not a valid email',
                'unique' => $data['email']. ' email has already been taken by someone else'
            ]
        );
    }

    private function rules()
    {
        $rules_array =  [
            'roles' => 'required|array',
            'email' => 'email|unique:users'
        ];
        if(isset($this->request['companies'])) {
            array_push($rules_array, ['companies' => 'required|array']);
        }
        return $rules_array;
    }
}
