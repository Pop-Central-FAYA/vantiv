<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignDetail extends Model
{
    protected $table = 'campaignDetails';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

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
}
