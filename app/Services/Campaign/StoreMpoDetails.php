<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\MpoDetail;

class StoreMpoDetails
{
    protected $mpo_id;
    protected $broadcaster_id;
    protected $agency_id;
    protected $preselected_adslot_group;

    public function __construct($mpo_id, $broadcaster_id, $agency_id, $preselected_adslot_group)
    {
        $this->mpo_id = $mpo_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->preselected_adslot_group = $preselected_adslot_group;
    }

    public function storeMpoDetails()
    {
        $mpo_details = new MpoDetail();
        $mpo_details->id = uniqid();
        $mpo_details->mpo_id = $this->mpo_id;
        $mpo_details->discount = 0;
        $mpo_details->agency_id = $this->agency_id ? $this->agency_id : '';
        $mpo_details->agency_broadcaster = $this->agency_id ? $this->preselected_adslot_group->broadcaster_id : '';
        $mpo_details->broadcaster_id = $this->agency_id ? $this->preselected_adslot_group->broadcaster_id : $this->broadcaster_id;
        $mpo_details->save();
        return $mpo_details;
    }
}
