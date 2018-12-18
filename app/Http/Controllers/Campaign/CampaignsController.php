<?php

namespace Vanguard\Http\Controllers\Campaign;

use Vanguard\Http\Controllers\Controller;
use Session;

class CampaignsController extends Controller
{
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct()
    {
        $this->broadcaster_id = Session::get('broadcaster_id');
        $this->agency_id = Session::get('agency_id');
    }
}
