<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Services\Client\ClientDetails;

class StoreCampaignGeneralInformation
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function run()
    {
        return $this->storeCampaignGeneralInformationInSession();
    }

    public function storeCampaignGeneralInformationInSession()
    {
        $client_details = new ClientDetails($this->request->client);

        $client_details = $client_details->run();

        $campaign_general_information = ((object) $this->request->all());

        session(['campaign_general_information' => $campaign_general_information]);

        return $client_details->id;
    }
}
