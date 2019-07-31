<?php

namespace Vanguard\Services\Brands;

use Vanguard\Models\Brands;

class StoreBrand
{
    protected $brand_details;

    public function __construct($brand_details)
    {
        $this->brand_details = $brand_details;
    }

    public function storeBrand()
    {
        $brand = new Brands();
        $brand->name = $this->brand_details->name;
        $brand->image_url = $this->brand_details->image_url;
        $brand->status = $this->brand_details->status;
        $brand->created_by = $this->brand_details->created_by;
        $brand->client_id = $this->brand_details->client_id;
        $brand->save();
        return $brand;
    }

  
}


