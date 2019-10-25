<?php

namespace Vanguard\Services\Station;

use Vanguard\Models\TvStation;
use Vanguard\Services\BaseServiceInterface;

class StoreService implements BaseServiceInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run()
    {
        $station = new TvStation();
        $station->publisher_id = $this->data['publisher_id'];
        $station->name = $this->data['name'];
        $station->type = $this->data['type'];
        $station->state = $this->data['state'];
        $station->city = $this->data['city'];
        $station->region = $this->data['region'];
        $station->key = $this->data['key'];
        $station->broadcast = $this->data['broadcast'];
        $station->save();
        return $station;
    }
}