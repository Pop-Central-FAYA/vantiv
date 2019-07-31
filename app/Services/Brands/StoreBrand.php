<?php

namespace Vanguard\Services\Brands;

class StoreBrand
{
    protected $brand_details;

    public function __construct($brand_details)
    {
        $this->brand_details = $brand_details;
    }

    public function store()
    {
        return \DB::table('brands')->insert(
            [
                'id' => $this->client_details->id,
                'name' => $this->client_details->name,
                'image_url' => $this->client_details->image_url,
                'status' => $this->client_details->status,
                'created_by' => $this->client_details->created_by,
                'client_id' => $this->client_details->client_id
            ]
        );
    }
}


