<?php

namespace Vanguard\Services\MediaAsset;
use Vanguard\Models\MediaAsset;
use Auth;

class CreateMediaAsset
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function run()
    {
        return $this->create($this->request);
    }

    public function create(array $data)
    {
        return MediaAsset::create([
            'client_id' => $data['client_id'],
            'brand_id' => $data['brand_id'],
            'media_type' => $data['media_type'],
            'asset_url' => $data['asset_url'],
            'regulatory_cert_url' => $data['regulatory_cert_url'],
            'file_name' => $data['file_name'],
            'duration' => $data['duration'],
            'company_id' => Auth::user()->companies->first()->id,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id
        ]);
    }
}