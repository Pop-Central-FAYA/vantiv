<?php

namespace Vanguard\Models;

class TimeBeltTransaction extends Base
{
    protected $fillable = ['time_belt_id', 'media_program_id', 'playout_date', 'duration', 'file_name',
                            'file_url', 'campaign_details_id', 'file_format', 'amount_paid', 'playout_hour',
                            'approval_status', 'payment_status'];

    public function time_belt()
    {
        return $this->belongsTo(TimeBelt::class);
    }

    public function campaign_detail()
    {
        return $this->belongsTo(CampaignDetail::class);
    }
}
