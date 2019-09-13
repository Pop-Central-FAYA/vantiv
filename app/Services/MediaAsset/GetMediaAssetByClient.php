<?php
namespace Vanguard\Services\MediaAsset;

use Illuminate\Support\Facades\DB;
use Vanguard\Models\MediaAsset;

class GetMediaAssetByClient
{
    /**
     * construct params
     */

     protected $client_id;
     protected $brand_id;

    public function __construct($client_id, $brand_id)
    {
        $this->client_id = $client_id;
        $this->brand_id = $brand_id;
    }

    public function run()
    {
        return MediaAsset::where('client_id', $this->client_id)
                        ->where('brand_id', $this->brand_id)
                        ->get();
    }
}