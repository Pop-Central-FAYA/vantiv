<?php

namespace Vanguard\Services\Company;
use Illuminate\Support\Arr;
use DB;


/**
 * This service is to update a company.
 */
class UpdateCompany
{ 
    const COMPANY_UPDATE_FIELDS = ['logo', 'address'];
    protected $company;
    protected $data;

    public function __construct($company, $data)
    {
        $this->company = $company;
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
            $this->updateModel($this->company, static::COMPANY_UPDATE_FIELDS, $this->data);
            return $this->company;
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

