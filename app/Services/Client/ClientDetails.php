<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\WalkIns;

class ClientDetails
{
    private $client_id;

    public function __construct($client_id)
    {
        $this->client_id = $client_id;
    }

    public function run()
    {
        return WalkIns::where('id', $this->client_id)->first();
    }
}
