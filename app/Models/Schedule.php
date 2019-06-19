<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends TimeBeltTransaction
{
    protected $table = 'time_belt_transactions';

    protected $fillable = [
        'order', 'playout_date', 'duration', 'file_name', 'file_url', 'playout_hour', 'company_id', 'ad_break'
    ];

    public function station()
    {
        return $this->belongsTo(Company::class,'company_id', 'id');
    }

}
