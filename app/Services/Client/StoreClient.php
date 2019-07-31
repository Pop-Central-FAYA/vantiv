<?php

namespace Vanguard\Services\Client;

class StoreClient
{
    protected $client_details;

    public function __construct($client_details)
    {
        $this->client_details = $client_details;
    }

    public function store()
    {
        return \DB::table('clients')->insert(
            [
                'id' => $this->client_details->id,
                'name' => $this->client_details->name,
                'image_url' => $this->client_details->image_url,
                'status' => $this->client_details->status,
                'created_by' => $this->client_details->created_by,
                'company_id' => $this->client_details->company_id,
                'street_address' => $this->client_details->street_address,
                'city' => $this->client_details->city,
                'state' => $this->client_details->state,
                'nationality' => $this->client_details->nationality
            ]
        );
    }
}

