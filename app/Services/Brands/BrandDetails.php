<?php

namespace Vanguard\Services\Brands;

use Vanguard\Models\Brand;

class BrandDetails
{
    protected $brand_id;

    public function __construct($brand_id)
    {
        $this->brand_id = $brand_id;
    }

    public function getBrandDetails()
    {
        return Brand::where('id', $this->brand_id)->first();
    }
}
