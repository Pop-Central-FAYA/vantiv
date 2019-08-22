<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Base
{
    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
       'invoice_id', 'user_id', 'broadcaster_id', 'invoice_number', 'actual_amount_paid',
        'refunded_amount', 'status', 'walkins_id', 'agency_id', 'agency_broadcaster'
    ];

    public $timestamps = false;

    public function invoice()
    {
        return $this->belongsTo(\Vanguard\Models\Invoice::class);
    }
}
