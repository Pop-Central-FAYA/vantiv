<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'campaign_id', 'campaign_reference', 'total', 'status', 'campaign_budget'
    ];

    public $timestamps = false;
}
