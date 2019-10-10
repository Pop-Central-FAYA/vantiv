<?php

namespace Vanguard\Listeners\Dsp\Campaign;

use Carbon\Carbon;
use Vanguard\Events\Dsp\Campaign\UpdateStatus;
use Vanguard\Libraries\Enum\Dsp\CampaignStatus;

class CompleteStatus
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
        $status = $campaign->status;
        if($status === CampaignStatus::ACTIVE) {
            if($this->hasCampaignEnded($campaign->stop_date)){
                $campaign->status = CampaignStatus::COMPLETED;
                $campaign->save();
                return $campaign;
            }
        }
        return $campaign;
    }

    private function hasCampaignEnded($campaign_end_date)
    {
        $today = Carbon::now();
        $campaign_end_date = Carbon::parse($campaign_end_date);
        return $campaign_end_date > $today;
    }
}
