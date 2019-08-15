<?php

namespace Vanguard\Services\Brands;
use Illuminate\Support\Arr;
use DB;
use Vanguard\Services\BaseServiceInterface;


/**
 * This service is to update a company.
 */
class UpdateBrand implements BaseServiceInterface
{ 
    const COMPANY_UPDATE_FIELDS = ['name', 'image_url'];
    protected $brand;
    protected $data;

    public function __construct($brand, $data)
    {
        $this->brand = $brand;
        $this->data = $data;
    }

    public function run()
    {
        return $this->update();
    }

    /**
     * Update the company
     * @return \Vanguard\Models\Company  The model holding the company
     */
    protected function update()
    {
        return DB::transaction(function () {
            $this->updateModel($this->brand, static::COMPANY_UPDATE_FIELDS, $this->data);
            return $this->brand;
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
}

