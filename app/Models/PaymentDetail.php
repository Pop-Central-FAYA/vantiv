<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $table = 'paymentDetails';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'payment_id', 'payment_method', 'amount', 'payment_status', 'status',
        'broadcaster', 'walkins_id', 'agency_id', 'agency_broadcaster', 'campaign_budget'
    ];

    public $timestamps = false;
}
