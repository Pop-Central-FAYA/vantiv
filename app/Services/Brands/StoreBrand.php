<?php

namespace Vanguard\Services\Brands;

use Vanguard\Models\Brand;
use Vanguard\Services\IService;

class StoreBrand implements IService
{
    protected $brand_details;
    protected $client_id;
    protected $user;

    public function __construct($brand_details, $client_id, $user)
    {
        $this->brand_details = (object)$brand_details;
        $this->user = $user;
        $this->client_id = $client_id;
    }

    public function run()
    {
        $brand = new Brand();
        $brand->name = $this->brand_details->name;
        $brand->image_url = $this->brand_details->image_url;
        $brand->status = $this->brand_details->status;
        $brand->created_by =  $this->user->id;
        $brand->client_id = $this->client_id;
        $brand->save();
        return $brand;
    }

  
}


