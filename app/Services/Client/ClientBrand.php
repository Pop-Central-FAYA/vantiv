<?php

namespace Vanguard\Services\Client;

use Vanguard\Libraries\Utilities;

class ClientBrand
{
    protected $client_id;

    public function __construct($client_id)
    {
        $this->client_id = $client_id;
    }

    public function run()
    {
        return $this->getClientBrands();
    }

    public function getClientBrands()
    {
        return Utilities::switch_db('api')->table('brand_client')
                        ->JOIN('brands', 'brands.id', '=', 'brand_client.brand_id')
                        ->select('brands.*',
                                          'brand_client.media_buyer_id AS agency_broadcaster',
                                          'brand_client.client_id AS client_walkins_id'
                        )
                        ->where('brand_client.client_id', $this->client_id)
                        ->get();
    }
}
