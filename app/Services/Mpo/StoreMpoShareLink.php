<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Models\MpoShareLink;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class StoreMpoShareLink
{
    protected $mpo_id;
    protected $campaign_stop_date;
    
    public function __construct($mpo_id, $campaign_stop_date)
    {
        $this->mpo_id = $mpo_id;
        $this->campaign_stop_date = $campaign_stop_date;
    }

    public function run()
    {
        return $this->storeMpoShareLink();
    }

    private function storeMpoShareLink()
    {
        $share_link = new MpoShareLink();
        $share_link->id = $id = \uniqid();
        $share_link->mpo_id = $this->mpo_id;
        $share_link->url = URL::signedRoute('guest.mpo_share_link', ['id' => $id]);
        $share_link->expired_at = Carbon::parse($this->campaign_stop_date)->addDays(90);
        $share_link->code = uniqid();
        $share_link->save();
        return $share_link;
    }
}