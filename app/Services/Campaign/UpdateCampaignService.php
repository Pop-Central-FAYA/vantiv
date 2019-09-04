<?php

namespace Vanguard\Services\Campaign;

use Illuminate\Support\Arr;
use Vanguard\Services\BaseServiceInterface;

class UpdateCampaignService implements BaseServiceInterface
{
    const CAMPAIGN_UPDATE_FIELDS = ['budget'];

    protected $data;
    protected $campaign;

    public function __construct($campaign, $data)
    {
        $this->data = $data;
        $this->campaign = $campaign;
    }

    public function run()
    {
        return $this->update();
    }

    protected function update()
    {
        $this->updateModel(static::CAMPAIGN_UPDATE_FIELDS, $this->data);
        return $this->campaign;
    }

    private function updateModel($update_fields, $data)
    {
        foreach ($update_fields as $key) {
            if (Arr::has($data, $key)) {
                $this->campaign->setAttribute($key, $data[$key]);
            }
        }
        $this->campaign->save();
    }
}