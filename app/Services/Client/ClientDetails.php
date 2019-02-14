<?php

namespace Vanguard\Services\Client;

use Vanguard\Libraries\Utilities;

class ClientDetails
{
    public $id;
    public $client_id;

    public function __construct($id, $client_id)
    {
        $this->id = $id;
        $this->client_id = $client_id;
    }

    public function run()
    {
        return \DB::table('walkIns')
                            ->when($this->id, function($query) {
                                return $query->where('id', $this->id);
                            })
                            ->when($this->client_id, function($query) {
                                return $query->where('user_id', $this->client_id);
                            })
                            ->first();
    }
}
