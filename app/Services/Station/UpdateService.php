<?php

namespace Vanguard\Services\Station;

use Illuminate\Support\Arr;
use Vanguard\Services\BaseServiceInterface;

class UpdateService implements BaseServiceInterface
{
    protected $station_id;
    protected $data;
    protected $station;

    const STATION_UPDATE_FIELDS = ['name', 'type', 'state', 'city', 'region', 'broadcast'];

    public function __construct($station_id, $data, $station)
    {
        $this->station_id = $station_id;
        $this->data = $data;
        $this->station = $station;
    }

    public function run()
    {
        $this->updateModel($this->station, static::STATION_UPDATE_FIELDS, $this->data);
    }

    /**
     * Setting attributes like this, so that events are fired
     * if we just do a model update directly from array, events will not be fired
     */
    private function updateModel($model, $update_fields, $data)
    {
        foreach ($update_fields as $key) {
            if (Arr::has($data, $key)) {
                $model->setAttribute($key, $data[$key]);
            }
        }
        //save will only actually save if the model has changed
        $model->save();
    }
}