<?php

namespace Vanguard\Services\Campaign;

use Session;

class AllCampaignService
{
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct()
    {
        $this->broadcaster_id = Session::get('broadcaster_id');
        $this->agency_id = Session::get('agency_id');
    }

    public function run()
    {

    }

    public function campaignsDataToDatatables()
    {

    }
}
