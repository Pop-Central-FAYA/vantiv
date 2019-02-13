<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\WalkIns;

class AllClient
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function run()
    {
        return $this->getAllClients();
    }

    public function getAllClients()
    {
        return WalkIns::where('company_id', $this->company_id)->get();
    }
}
