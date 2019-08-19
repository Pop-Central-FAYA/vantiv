<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\MpoShareLinkActivity;

class StoreMpoShareLinkActivity
{
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function run()
    {
        $activity = new MpoShareLinkActivity();
        $activity->description = $this->message;
        $activity->ip_address = request()->ip();
        $activity->user_agent = request()->header('User-Agent');
        $activity->save();
    }
}