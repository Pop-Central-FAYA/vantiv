<?php

namespace Vanguard\Services\Campaign;

use Log;

class TotalVolumeCampaignChart
{
    const MONTHS_OF_THE_YEAR = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 
        'September', 'October', 'November', 'December');

    protected $companies_id;

    public function __construct($companies_id)
    {
        $this->companies_id = $companies_id;
    }

    public function getTotalVolumeOfCampaignQuery()
    {
        return \DB::table('campaignDetails')
                // ->selectRaw("COUNT(id) as volume, DATE_FORMAT(time_created, '%M, %Y') as month")
                ->selectRaw("COUNT(id) as volume, DATE_FORMAT(time_created, '%M') as month")
                ->where([
                    ['status', '!=', 'on_hold'],
                    ['launched_on', $this->companies_id]
                ])
                // ->groupBy(\DB::raw("DATE_FORMAT(time_created, '%Y-%m')"))
                ->groupBy(\DB::raw("DATE_FORMAT(time_created, '%M')"))
                ->get();
    }

    public function totalVolumeOfCampaign()
    {
        $months = collect(static::MONTHS_OF_THE_YEAR);
        $collection = $this->getTotalVolumeOfCampaignQuery();
        $campaign_volumes = [];
        $campaign_months = [];
        foreach (static::MONTHS_OF_THE_YEAR as $month) {
            $month_val = $collection->firstWhere("month", $month);
            $volume = 0;
            if ($month_val) {
                // $volume = $month_val['volume'];
                $volume = $month_val->volume;
            }
            $campaign_volumes[] = $volume;
            $campaign_months[] = $month;
        }
        return ['campaign_volumes' => json_encode($campaign_volumes), 'campaign_months' => json_encode($campaign_months)];
    }
}
