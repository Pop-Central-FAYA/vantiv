<?php

namespace Vanguard\Services\Walkin;

use Vanguard\Models\WalkIns;

class UpdateWalkIns
{
    protected $company_logo;
    protected $company_address;
    protected $company_name;
    protected $client_id;

    public function __construct($company_logo, $company_name, $company_address, $client_id)
    {
        $this->company_logo = $company_logo;
        $this->company_name = $company_name;
        $this->company_address = $company_address;
        $this->client_id = $client_id;
    }

    public function updateWalkIns()
    {
        $walkin = WalkIns::where('id', $this->client_id)->first();
        $walkin->company_logo = $this->company_logo ? $this->company_logo : $walkin->company_logo;
        $walkin->location = $this->company_address;
        $walkin->company_name = $this->company_name;
        $walkin->save();
        return $walkin;
    }
}
