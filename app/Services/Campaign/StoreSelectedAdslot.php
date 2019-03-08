<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\SelectedAdslot;

class StoreSelectedAdslot
{
    protected $campaign_id;
    protected $preselected_adslots;
    protected $user_id;
    protected $now;
    protected $agency_id;
    protected $broadcaster_id;

    public function __construct($campaign_id, $preselected_adslots, $user_id, $now, $agency_id, $broadcaster_id)
    {
        $this->campaign_id = $campaign_id;
        $this->preselected_adslots = $preselected_adslots;
        $this->user_id = $user_id;
        $this->now = $now;
        $this->agency_id = $agency_id;
        $this->broadcaster_id = $broadcaster_id;
    }

    public function storeSelectedAdslot()
    {
        $selected_adslot = new SelectedAdslot();
        $selected_adslot->id = uniqid();
        $selected_adslot->campaign_id = $this->campaign_id;
        $selected_adslot->file_name = $this->preselected_adslots->file_name;
        $selected_adslot->file_url = $this->preselected_adslots->file_url;
        $selected_adslot->adslot = $this->preselected_adslots->adslot_id;
        $selected_adslot->user_id = $this->user_id;
        $selected_adslot->file_code = Utilities::generateReference();
        $selected_adslot->created_at = date('Y-m-d H:i:s', $this->now);
        $selected_adslot->updated_at = date('Y-m-d H:i:s', $this->now);
        $selected_adslot->agency_id = $this->agency_id;
        $selected_adslot->agency_broadcaster = $this->preselected_adslots->broadcaster_id;
        $selected_adslot->time_picked = $this->preselected_adslots->time;
        $selected_adslot->broadcaster_id = $this->agency_id ? $this->preselected_adslots->broadcaster_id : $this->broadcaster_id;
        $selected_adslot->public_id = '';
        $selected_adslot->format = $this->preselected_adslots->format;
        $selected_adslot->status = CampaignStatus::PENDING;
        $selected_adslot->air_date = $this->preselected_adslots->air_date;
        $selected_adslot->adslot_amount = $this->preselected_adslots->total_price;

        $selected_adslot->save();
        return $selected_adslot;
    }
}
