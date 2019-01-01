<?php

namespace Vanguard\Services\Broadcaster;

use Vanguard\Models\Broadcaster;

class BroadcasterDetails
{
    protected $broadcaster_id;

    public function __construct($broadcaster_id)
    {
        $this->broadcaster_id = $broadcaster_id;
    }

    public function getBroadcasterDetails()
    {
        return Broadcaster::where('id', $this->broadcaster_id)->first();
    }
}
