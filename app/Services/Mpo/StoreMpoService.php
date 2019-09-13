<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\CampaignMpo;
use Vanguard\Services\BaseServiceInterface;

class StoreMpoService implements BaseServiceInterface
{
    protected $data;
    protected $campaign_id;

    public function __construct($data, $campaign_id)
    {
        $this->data = $data;
        $this->campaign_id = $campaign_id;
    }

    public function run()
    {
        $campaign_mpo = new CampaignMpo();
        $campaign_mpo->campaign_id = $this->campaign_id;
        $campaign_mpo->ad_vendor_id = $this->data['ad_vendor_id'];
        $campaign_mpo->insertions = $this->data['insertions'];
        $campaign_mpo->net_total = $this->data['net_total'];
        $campaign_mpo->adslots = json_encode($this->data['adslots']);
        $campaign_mpo->save();
        return $campaign_mpo;
    }
}