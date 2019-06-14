<?php

namespace Vanguard\Services\MediaAsset;
use Validator;

class ValidateCreateAsset
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function run()
    {
        return $this->validator($this->request);
    }

    /**
     * Get a validator for an incoming criteria request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'client_id' => 'required|string',
            'brand_id' => 'required|string',
            'media_type' => 'required|string',
            'asset_url' => 'required|string',
            'regulatory_cert_url' => 'required|string',
            'file_name' => 'required|string',
            'duration' => 'required|integer'
        ];
        return Validator::make($data, $rules,
            [
                'required' => ':attribute is required',
                'unique' => ':attribute already exists',
            ]
        );
    }
}