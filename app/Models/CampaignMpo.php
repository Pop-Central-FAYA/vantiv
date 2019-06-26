<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMpo extends Base
{
    protected $table = 'campaign_mpos';
    protected $fillable = ['campaign_id', 'station', 'ad_slots'];
}
