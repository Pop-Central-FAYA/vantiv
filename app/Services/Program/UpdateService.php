<?php

namespace Vanguard\Services\Program;

use Illuminate\Support\Arr;
use Vanguard\Services\BaseServiceInterface;

class UpdateService extends StoreService implements BaseServiceInterface
{
    protected $program_id;
    protected $data;
    protected $program;

    const PROGRAM_UPDATE_FIELDS = ['program_name', 'attributes'];

    public function __construct($program_id, $data, $program)
    {
        $this->program_id = $program_id;
        $this->data = $data;
        $this->program = $program;
    }

    public function run()
    {
        $this->updateModel($this->program, static::PROGRAM_UPDATE_FIELDS, $this->setUpData());
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

    private function setUpData()
    {
        return [
            'program_name' => $this->data['program_name'],
            'attributes' => json_encode($this->formatAttribute())
        ];
    }
}