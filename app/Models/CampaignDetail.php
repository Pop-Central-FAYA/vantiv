<?php

namespace Vanguard\Models;

class CampaignDetail extends Base
{
    protected $table = 'campaignDetails';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'user_id', 'broadcaster', 'brand', 'name', 'product',
        'channel', 'start_date', 'stop_date', 'campaign_status', 'status',
        'day_parts', 'target_audience', 'min_age', 'max_age', 'Industry',
        'sub_industry','adslots', 'region', 'walkins_id', 'adslots_id',
        'agency', 'agency_broadcaster', 'campaign_id'
    ];

    public $timestamps = false;

    public function time_belt_transactions()
    {
        return $this->hasMany(TimeBeltTransaction::class);
    }
}
