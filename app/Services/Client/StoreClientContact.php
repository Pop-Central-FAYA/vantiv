<?php

namespace Vanguard\Services\Client;

class StoreClient
{
    protected $client_contact_details;

    public function __construct($client_contact_details)
    {
        $this->client_contact_details = $client_contact_details;
    }

    public function store()
    {
        return \DB::table('client_contacts')->insert(
            [
                'id' => $this->client_contact_details->id,
                'client_id' => $this->client_contact_details->client_id,
                'first_name' => $this->client_contact_details->first_name,
                'last_name' => $this->client_contact_details->last_name,
                'email' => $this->client_contact_details->email,
                'phone_number' => $this->client_contact_details->phone_number,
                'is_primary' => $this->client_contact_details->is_primary
            ]
        );
    }
}


