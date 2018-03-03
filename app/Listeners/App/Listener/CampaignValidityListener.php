<?php

namespace Vanguard\Listeners\App\Listener;

use Vanguard\Events\App\Events\CampaignValidity;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CampaignValidityListener
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
     * @param  CampaignValidity  $event
     * @return void
     */
    public function handle(CampaignValidity $event)
    {
        //
    }
}
