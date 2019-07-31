<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\Client;

class StoreClient
{
    protected $client_details;

    public function __construct($client_details)
    {
        $this->client_details = $client_details;
    }

    public function storeClient()
    {
        $client = new Client();
        $client->name = $this->client_details->name;
        $client->brand = $this->client_details->brand;
        $client->image_url =  $this->client_details->image_url;
        $client->status = $this->client_details->status;
        $client->created_by = $this->client_details->created_by;
        $client->company_id = $this->client_details->company_id;
        $client->street_address = $this->client_details->street_address;
        $client->city = $this->client_details->city;
        $client->state = $this->client_details->state;
        $client->nationality = $this->client_details->nationality;
        $client->save();
        return $client;
    }
}
