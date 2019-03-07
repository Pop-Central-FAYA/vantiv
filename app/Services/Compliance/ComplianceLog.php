<?php

namespace Vanguard\Services\Compliance;

use Vanguard\Libraries\Enum\BroadcasterPlayoutStatus;

class ComplianceLog
{
    protected $company_id;
    protected $campaign_id;

    public function __construct($company_id, $campaign_id)
    {
        $this->company_id = $company_id;
        $this->campaign_id = $campaign_id;
    }

    public function getComplianceLog()
    {
        return \DB::table('broadcaster_playouts')
            ->join('companies', 'broadcaster_playouts.broadcaster_id', '=', 'companies.id')
            ->join('selected_adslots', 'broadcaster_playouts.selected_adslot_id', '=', 'selected_adslots.id')
            ->join('mpos', 'broadcaster_playouts.mpo_detail_id', '=', 'mpos.id')
            ->select('companies.id AS broadcaster_id','companies.name AS broadcaster_station',
                'selected_adslots.file_name AS asset_name','selected_adslots.time_picked AS duration',
                'broadcaster_playouts.status AS compliance_status', 'broadcaster_playouts.played_at AS played_date',
                'mpos.campaign_id AS campaign_id', 'selected_adslots.air_date AS schedule_date',
                'broadcaster_playouts.air_between AS schedule_spot')
            ->when($this->company_id, function($query) {
                return $query->where('broadcaster_playouts.broadcaster_id', $this->company_id);
            })
            ->where([
                        ['mpos.campaign_id', $this->campaign_id],
                        ['broadcaster_playouts.status', BroadcasterPlayoutStatus::PLAYED]
                    ])
            ->get();
    }
}
