<?php

namespace Vanguard\Listeners\Dsp;

use Vanguard\Events\Dsp\CampaignMpoTimeBeltUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Vanguard\Services\Campaign\UpdateCampaignService;

class CampaignBudgetUpdated
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
     * @param  CampaignMpoTimeBeltUpdated  $event
     * @return void
     */
    public function handle(CampaignMpoTimeBeltUpdated $event)
    {
        $campaign = $event->campaign_mpo->campaign;
        $campaign_mpos = $campaign->campaign_mpos;
        $sum_budget = $campaign_mpos->sum('budget');
        return (new UpdateCampaignService($campaign, ['budget' => $sum_budget]))->run();
    }
}
