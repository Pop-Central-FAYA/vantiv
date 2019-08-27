<?php

namespace Vanguard\Services\Brands;

use Vanguard\Models\Brand;
use Vanguard\Services\BaseServiceInterface;
use DB;

class StoreBrand implements BaseServiceInterface
{
    protected $brand_details;
    protected $client_id;
    protected $user_id;

    public function __construct($brand_details, $client_id, $user_id)
    {
        $this->brand_details = $brand_details;
        $this->user_id = $user_id;
        $this->client_id = $client_id;
    }

    public function run()
    {
        return DB::transaction(function () {
        $brand = new Brand();
        $brand->name = $this->brand_details['name'];
        $brand->image_url = $this->brand_details['image_url'];
        $brand->created_by =  $this->user_id;
        $brand->client_id = $this->client_id;
        $brand->save();
        return $brand;
        });
    }

  
}


