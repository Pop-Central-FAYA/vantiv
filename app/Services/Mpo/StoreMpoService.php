<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Libraries\Enum\MpoStatus;
use Vanguard\Models\CampaignMpo;
use Vanguard\Services\BaseServiceInterface;
use Vanguard\Libraries\Utilities;

class StoreMpoService implements BaseServiceInterface
{
    protected $data;
    protected $campaign_id;

    const AD_VENDOR = 'ad_vendor_id';
    const PUBLISHER = 'publisher_id';

    protected $status = [MpoStatus::ACCEPTED, MpoStatus::SUBMITTED];

    public function __construct($data, $campaign_id)
    {
        $this->data = $data;
        $this->campaign_id = $campaign_id;
    }

    public function run()
    {
        $this->updatePreviousMpo();
        $campaign_mpo = new CampaignMpo();
        $campaign_mpo->campaign_id = $this->campaign_id;
        $campaign_mpo->ad_vendor_id = $this->data['group'] === self::AD_VENDOR ? $this->data['ad_vendor_id'] : '';
        $campaign_mpo->publisher_id = $this->data['group'] === self::PUBLISHER ? $this->data['publisher_id'] : '';
        $campaign_mpo->insertions = $this->data['insertions'];
        $campaign_mpo->net_total = $this->data['net_total'];
        $campaign_mpo->adslots = json_encode($this->data['adslots']);
        $campaign_mpo->reference_number = Utilities::generateReference();
        $campaign_mpo->status = MpoStatus::PENDING;
        $campaign_mpo->version = $this->getLatestVersion();
        $campaign_mpo->save();
        return $campaign_mpo;
    }

    private function getLatestVersion()
    {
        $last_mpo = CampaignMpo::where('campaign_id', $this->campaign_id);
        $last_mpo = $this->columnToQuery($last_mpo)
                        ->orderBy('created_at', 'DESC')
                        ->first();
        return $last_mpo ? $last_mpo->version + 1 : 1;
    }
    
    private function updatePreviousMpo()
    {
        $mpo = CampaignMpo::where('campaign_id', $this->campaign_id);
        $mpo = $this->columnToQuery($mpo)
                    ->whereIn('status', $this->status)
                    ->update(['status' => MpoStatus::CLOSED]);
    }

    private function columnToQuery($mpo)
    {
        return $mpo->when($this->data['group'] === self::AD_VENDOR, function($query) {
                    $query->where('ad_vendor_id', $this->data['ad_vendor_id']);
                })
                ->when($this->data['group'] === self::PUBLISHER, function($query) {
                    $query->where('publisher_id', $this->data['publisher_id']);
                });
    }
}