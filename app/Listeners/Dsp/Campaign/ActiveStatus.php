<?php

namespace Vanguard\Listeners\Dsp\Campaign;

use Carbon\Carbon;
use Vanguard\Events\Dsp\Campaign\UpdateStatus;
use Vanguard\Libraries\Enum\Dsp\CampaignStatus;
use Vanguard\Libraries\Enum\MpoStatus;

class ActiveStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UpdateStatus  $event
     * @return void
     */
    public function handle(UpdateStatus $event)
    {
        $campaign = $event->campaign;
        $mpos = $campaign->campaign_mpos;
        $status = $campaign->status;
        if($status === CampaignStatus::PENDING){
            if($this->hasAcceptedMpo($mpos) && $this->hasCampaignBegun($campaign->start_date)){
                $campaign->status = CampaignStatus::ACTIVE;
                $campaign->save();
                return $campaign;
            }
        }
        return $campaign;
    }

    private function hasAcceptedMpo($mpos)
    {
        return $mpos->first(function($mpo) {
            return $mpo->status === MpoStatus::ACCEPTED;
        });
    }

    private function hasCampaignBegun($campaign_start_date)
    {
        $today = Carbon::now();
        $campaign_start_date = Carbon::parse($campaign_start_date);
        return $today >= $campaign_start_date;
    }
}
