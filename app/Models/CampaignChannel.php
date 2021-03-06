<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignChannel extends Model
{
    protected $table = 'campaignChannels';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'channel', 'status'
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'channel_company', 'channel_id', 'company_id');
    }
}
