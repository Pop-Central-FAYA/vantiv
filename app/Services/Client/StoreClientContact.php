<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\ClientContact;
use Vanguard\Services\IService;

class StoreClientContact implements IService
{
    protected $client_contact_details;
    protected $client;

    public function __construct($client, $client_contact_details)
    {
        $this->client_contact_details = $client_contact_details;
        $this->client = $client;
    }

    public function run()
    {
       return $this->client->client_contact()->create($this->client_contact_details);
    }
}