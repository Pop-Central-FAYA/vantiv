<?php

namespace Vanguard\Listeners\Dsp;

use Vanguard\Events\Dsp\CampaignMpoTimeBeltUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Vanguard\Services\Mpo\UpdateMpoService;

class CampaignMpoUpdated
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
        $time_belts = $event->campaign_mpo->campaign_mpo_time_belts;
        $sum_insertions = $time_belts->sum('ad_slots');
        $sum_net_total = $time_belts->sum('net_total');
        return (new UpdateMpoService($event->campaign_mpo, ['budget' => $sum_net_total, 'ad_slots' => $sum_insertions]))->run();
    }
}
