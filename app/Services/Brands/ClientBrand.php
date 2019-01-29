<?php

namespace Vanguard\Services\Brands;

use Vanguard\Libraries\Utilities;

class ClientBrand
{
    protected $brand_slug;
    protected $client_id;

    public function __construct($brand_slug, $client_id)
    {
        $this->brand_slug = $brand_slug;
        $this->client_id = $client_id;
    }

    public function getClientBrands()
    {
        return Utilities::switch_db('api')->table('brand_client')
                        ->join('brands', 'brands.id', '=', 'brand_client.brand_id')
                        ->select('brands.*')
                        ->where([
                            ['brands.slug', $this->brand_slug],
                            ['brand_client.client_id', $this->client_id]
                        ])
                        ->get();

    }

    public function checkForBrandExistence()
    {
        $brands = $this->getClientBrands();
        if(count($brands) > 0){
            return 'brand_exist';
        }
    }
}
