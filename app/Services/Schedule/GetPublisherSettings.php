<?php

namespace Vanguard\Services\Schedule;

use Vanguard\Models\Publisher;

class GetPublisherSettings
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function run()
    {
        return json_decode(Publisher::where('company_id', $this->company_id)->first()->settings, true);
    }
}