<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Services\Traits\GetDayPartTrait;

class ExportCampaignMpoSummary
{
    protected $campaign_mpo_details;

    use GetDayPartTrait;

    public function __construct($campaign_mpo_details)
    {
        $this->campaign_mpo_details = $campaign_mpo_details;
    }

    public function run()
    {
        return $this->getMpoSummaryData();
    }

    private function getMpoSummaryData()
    {
        $summary_data = [];
        foreach($this->campaign_mpo_details as $duration => $campaign_mpo){
            $summary_data[] = [
                'duration' => $duration,
                'day_part' => $this->getDayPart($campaign_mpo[0]['time_belt_start_time'])['name'],
                'total_spot' => $campaign_mpo->sum('ad_slots'),
                'agency_percentage' => 15,
                'volume_percent' => $campaign_mpo[0]['volume_discount'],
                'total' => $campaign_mpo->sum('net_total')
            ];
        }
        return $summary_data;
    }
}