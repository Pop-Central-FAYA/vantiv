<?php

namespace Vanguard\Services\Brands;

use Vanguard\Libraries\Utilities;
use Vanguard\Models\Brand;

class BrandDetails
{
    protected $brand_id;
    protected $brand_slug;

    public function __construct($brand_id, $brand_slug)
    {
        $this->brand_id = $brand_id;
        $this->brand_slug = $brand_slug;
    }

    public function getBrandDetails()
    {
        return Utilities::switch_db('api')->table('brands')
                        ->when($this->brand_id, function ($query) {
                            return $query->where('id', $this->brand_id);
                        })
                        ->when($this->brand_slug, function ($query) {
                            return $query->where('slug', $this->brand_slug);
                        })
                        ->first();

    }

}
