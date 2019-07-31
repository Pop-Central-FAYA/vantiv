<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\ClientContact;

class StoreClientContact
{
    protected $client_contact_details;

    public function __construct($client_contact_details)
    {
        $this->client_contact_details = $client_contact_details;
    }

    public function store()
    {
        $client_contact = new ClientContact();
        $client_contact->client_id = $this->client_contact_details->client_id;
        $client_contact->first_name = $this->client_contact_details->first_name;
        $client_contact->last_name =  $this->client_contact_details->last_name;
        $client_contact->email = $this->client_contact_details->email;
        $client_contact->phone_number = $this->client_contact_details->phone_number;
        $client_contact->is_primary = $this->client_contact_details->is_primary;
        $client_contact->save();
        return $client_contact;
    }
}