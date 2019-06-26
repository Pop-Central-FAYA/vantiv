<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Base
{
    protected $table = 'campaigns';
    protected $connection = 'api_db';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'campaign_status', 'reference'
    ];
    public $timestamps = false;
}
