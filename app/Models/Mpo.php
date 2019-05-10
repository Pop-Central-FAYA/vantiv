<?php

namespace Vanguard\Models;


class Mpo extends Base
{
    protected $table = 'mpos';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'campaign_id', 'campaign_reference', 'invoice_number', 'status'
    ];

    public $timestamps = false;
}
