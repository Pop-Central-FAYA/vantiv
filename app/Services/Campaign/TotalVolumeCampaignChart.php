<?php

namespace Vanguard\Services\Campaign;

class TotalVolumeCampaignChart
{
    protected $companies_id;

    public function __construct($companies_id)
    {
        $this->companies_id = $companies_id;
    }

    public function getTotalVolumeOfCampaignQuery()
    {
        return \DB::table('campaignDetails')
                ->selectRaw("COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as month")
                ->where([
                    ['status', '!=', 'on_hold'],
                    ['launched_on', $this->companies_id]
                ])
                ->groupBy(\DB::raw("DATE_FORMAT(time_created, '%Y-%m')"))
                ->get();
    }

    public function totalVolumeOfCampaign()
    {
        $campaign_volumes = [];
        $campaign_months = [];
        foreach ($this->getTotalVolumeOfCampaignQuery() as $total_volume_campaign){
            $campaign_volumes[] = $total_volume_campaign->volume;
            $campaign_months[] = $total_volume_campaign->month;
        }
        return ['campaign_volumes' => json_encode($campaign_volumes), 'campaign_months' => json_encode($campaign_months)];
    }
}
