<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = 'invoiceDetails';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'invoice_id', 'user_id', 'broadcaster_id', 'invoice_number', 'actual_amount_paid',
        'refunded_amount', 'status', 'walkins_id', 'agency_id', 'agency_broadcaster'
    ];

    public $timestamps = false;
}
