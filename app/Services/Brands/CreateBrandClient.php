<?php

namespace Vanguard\Services\Brands;

use Vanguard\Models\BrandClient;

class CreateBrandClient
{
    protected $broadcaster_id;
    protected $brand_id;
    protected $media_buyer_id;
    protected $client_id;

    public function __construct($broadcaster_id, $brand_id, $media_buyer_id, $client_id)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->brand_id = $brand_id;
        $this->media_buyer_id = $media_buyer_id;
        $this->client_id = $client_id;
    }

    public function storeClientBrand()
    {
        $brand_client = new BrandClient();
        $brand_client->brand_id = $this->brand_id;
        $brand_client->media_buyer = $this->broadcaster_id ? 'Broadcaster' : 'Agency';
        $brand_client->media_buyer_id = $this->media_buyer_id;
        $brand_client->client_id = $this->client_id;
        $brand_client->created_by = \Auth::user()->id;
        $brand_client->save();
        return $brand_client;
    }
}
