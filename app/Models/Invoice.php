<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Base
{
    protected $table = 'invoices';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
      'campaign_id', 'campaign_reference', 'invoice_number', 'status', 'payment_id'
    ];

    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(\Vanguard\Models\InvoiceDetail::class);
    }
}
