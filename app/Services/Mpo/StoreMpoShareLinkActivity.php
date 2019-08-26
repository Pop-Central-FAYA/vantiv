<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\MpoShareLinkActivity;

class StoreMpoShareLinkActivity
{
    protected $message;
    protected $share_link_id;

    public function __construct($message, $share_link_id)
    {
        $this->message = $message;
        $this->share_link_id = $share_link_id;
    }

    public function run()
    {
        $activity = new MpoShareLinkActivity();
        $activity->mpo_share_link_id = $this->share_link_id;
        $activity->description = $this->message;
        $activity->ip_address = request()->ip();
        $activity->user_agent = request()->header('User-Agent');
        $activity->save();
    }
}