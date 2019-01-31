<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Utilities;

class CampaignExtras
{
    protected $campaign_id;
    protected $broadcaster_id;
    protected $agency_id;

    public function __construct($campaign_id, $broadcaster_id, $agency_id)
    {
        $this->campaign_id = $campaign_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
    }

    public function checkStartDateAgainstCurrentDate()
    {
        $start_date = $this->fetchCampaignStartDate();
        $today = date('Y-m-d');
        if($today > $start_date->start_date){
            return 'error';
        }
        return;
    }

    public function fetchCampaignStartDate()
    {
        return  Utilities::switch_db('api')->table('campaignDetails')
                            ->select('start_date')
                            ->when($this->broadcaster_id, function($query) {
                                return $query->where('broadcaster', $this->broadcaster_id);
                            })
                            ->when($this->agency_id, function ($query) {
                                return $query->where('agency', $this->agency_id);
                            })
                            ->where('campaign_id', $this->campaign_id)
                            ->first();
    }
}
