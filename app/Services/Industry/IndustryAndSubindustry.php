<?php

namespace Vanguard\Services\Industry;

use Vanguard\Models\Sector;
use Vanguard\Models\SubSector;
use Vanguard\Services\Brands\BrandDetails;

class IndustryAndSubindustry
{
    protected $brand_id;

    public function __construct($brand_id)
    {
        $this->brand_id = $brand_id;
    }

    //This method gets the indusrty and sub industry a brand belongs to
    public function getBrandIndustryAndSubIndustry()
    {
        $brand = new BrandDetails($this->brand_id);
        $brand_details = $brand->getBrandDetails();
        $industry = Sector::where('sector_code', $brand_details->industry_code)->first();
        $sub_industry = SubSector::where('sub_sector_code', $brand_details->sub_industry_code)->first();

        return (['industry' => $industry, 'sub_industry' => $sub_industry]);
    }
}
