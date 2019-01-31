<?php

namespace Vanguard\Services\Brands;

use Vanguard\Models\Brand;

class CreateBrand
{
    protected $brand_name;
    protected $brand_logo;
    protected $industry;
    protected $sub_industry;
    protected $slug;

    public function __construct($brand_name, $brand_logo, $industry, $sub_industry, $slug)
    {
        $this->brand_name = $brand_name;
        $this->brand_logo = $brand_logo;
        $this->industry = $industry;
        $this->sub_industry = $sub_industry;
        $this->slug = $slug;
    }

    public function storeBrand()
    {
        $brand = new Brand();
        $brand->name = $this->brand_name;
        $brand->image_url = $this->brand_logo;
        $brand->industry_code = $this->industry;
        $brand->sub_industry_code = $this->sub_industry;
        $brand->slug = $this->slug;
        $brand->save();
        return $brand;
    }
}
