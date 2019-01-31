<?php

namespace Vanguard\Services\Walkin;

use Vanguard\Models\WalkIns;

class CreateWalkIns
{
    protected $user_id;
    protected $broadcaster_id;
    protected $agency_id;
    protected $company_image;
    protected $client_type;
    protected $address;
    protected $company_name;
    protected $broadcaster_id_from_request;

    public function __construct($user_id, $broadcaster_id, $agency_id, $company_image, $client_type, $address, $company_name, $broadcaster_id_from_request)
    {
        $this->user_id = $user_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->company_image = $company_image;
        $this->client_type = $client_type;
        $this->address = $address;
        $this->company_name = $company_name;
        $this->broadcaster_id_from_request = $broadcaster_id_from_request;
    }

    public function createWalkIn()
    {
        $walkin = new WalkIns();
        $walkin->user_id = $this->user_id;
        $walkin->broadcaster_id = $this->agency_id ? $this->broadcaster_id_from_request : $this->broadcaster_id;
        $walkin->client_type_id = $this->client_type;
        $walkin->location = $this->address;
        $walkin->agency_id = $this->agency_id ? $this->agency_id : '';
        $walkin->company_name = $this->company_name;
        $walkin->company_logo = $this->company_image;
        $walkin->save();
        return $walkin;
    }
}
