<?php

namespace Vanguard\Events\Dsp\Campaign;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Vanguard\Models\Campaign;

class UpdateStatus
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $campaign;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($campaign_id)
    {
        $this->campaign = Campaign::findOrFail($campaign_id);
    }
}
