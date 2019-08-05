<?php

namespace Vanguard\Services\Walkin;

use Vanguard\Models\WalkIns;

class CreateWalkIns
{
    protected $user_id;
    protected $company_image;
    protected $client_type;
    protected $address;
    protected $company_name;
    protected $broadcaster_id_from_request;
    protected $company_id;

    public function __construct($user_id, $company_image, $client_type, $address, $company_name,
                                $broadcaster_id_from_request, $company_id)
    {
        $this->user_id = $user_id;
        $this->company_image = $company_image;
        $this->client_type = $client_type;
        $this->address = $address;
        $this->company_name = $company_name;
        $this->broadcaster_id_from_request = $broadcaster_id_from_request;
        $this->company_id = $company_id;
    }

    public function createWalkIn()
    {
        $walkin = new WalkIns();
        $walkin->user_id = $this->user_id;
        $walkin->client_type_id = $this->client_type;
        $walkin->location = $this->address;
        $walkin->company_name = $this->company_name;
        $walkin->company_logo = $this->company_image;
        $walkin->company_id = $this->company_id;
        $walkin->save();
        return $walkin;
    }
}
