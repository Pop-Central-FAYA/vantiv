<?php

namespace Vanguard\Services\Client;

class BroadcasterClient
{
    protected $companies_id;

    public function __construct($companies_id)
    {
        $this->companies_id = $companies_id;
    }

    public function getCompanyClients()
    {
        return \DB::table('walkIns')
                    ->when(!is_array($this->companies_id), function ($query) {
                        return $query->where('broadcaster_id', $this->companies_id);
                    })
                    ->when(is_array($this->companies_id), function ($query) {
                        return $query->whereIn('broadcaster_id', $this->companies_id);
                    })
                    ->orderBy('time_created', 'DESC')
                    ->get();
    }
}
