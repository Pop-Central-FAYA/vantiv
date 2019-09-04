<?php

namespace Vanguard\Events\Dsp;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CampaignMpoTimeBeltUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $campaign_mpo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($campaign_mpo)
    {
        $this->campaign_mpo = $campaign_mpo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
