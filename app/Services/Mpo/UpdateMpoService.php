<?php

namespace Vanguard\Services\Mpo;

use Illuminate\Support\Arr;
use Vanguard\Services\BaseServiceInterface;

class UpdateMpoService implements BaseServiceInterface
{
    const CAMPAIGN_MPO_UPDATE_FIELDS = ['budget', 'adslots', 'status'];
    
    protected $mpo;
    protected $data;

    public function __construct($mpo, $data)
    {
        $this->mpo = $mpo;
        $this->data = $data;
    }

    public function run()
    {
        return $this->update();
    }

    protected function update()
    {
        $this->updateModel(static::CAMPAIGN_MPO_UPDATE_FIELDS, $this->data);
        return $this->mpo;
    }

    private function updateModel($update_fields, $data)
    {
        foreach ($update_fields as $key) {
            if (Arr::has($data, $key)) {
                $this->mpo->setAttribute($key, $data[$key]);
            }
        }
        $this->mpo->save();
    }
}