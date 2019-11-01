<?php

namespace Vanguard\Services\Program;

use DB;
use Illuminate\Support\Arr;
use Vanguard\Models\MediaPlanProgram;
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
        $this->update();
    }

    /**
     * Update the program, then update the ad vendors
     * @return \Vanguard\Models\MediaPlanProgram  The model holding the program
     */
    protected function update()
    {
        return DB::transaction(function () {
            $this->updateModel($this->program, static::PROGRAM_UPDATE_FIELDS, $this->setUpData());

            $ad_vendors = Arr::get($this->data, 'ad_vendors', []);
            $this->updateAdVendors($ad_vendors, $this->program);

            return $this->program;
        });
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

    protected function updateAdVendors(array $ad_vendor_list, MediaPlanProgram $program) {
        $ad_vendor_list = collect($ad_vendor_list);
        $program->ad_vendors()->sync($ad_vendor_list->pluck('id'));
    }
}