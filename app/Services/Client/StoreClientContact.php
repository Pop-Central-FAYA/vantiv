<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\ClientContact;

class StoreClientContact
{
    protected $client_contact_details;
    protected $client;

    public function __construct($client, $client_contact_details)
    {
        $this->client_contact_details = $client_contact_details;
        $this->client = $client;
    }

    public function store()
    {
       return $this->client->client_contact()->create($this->client_contact_details);
    }
}