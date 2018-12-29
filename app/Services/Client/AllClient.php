<?php

namespace Vanguard\Services\Client;

use Vanguard\Libraries\Utilities;

class AllClient
{
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($broadcaster_id, $agency_id)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
    }

    public function run()
    {
        return $this->getAllClients();
    }

    public function getAllClients()
    {
        return Utilities::switch_db('api')->table('walkIns')
                        ->when($this->broadcaster_id, function ($query) {
                            return $query->where('broadcaster_id', $this->broadcaster_id);
                        })
                        ->when($this->agency_id, function($query) {
                            return $query->where('agency_id', $this->agency_id);
                        })
                        ->get();
    }
}
