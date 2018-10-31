<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class PreselectedAdslot extends Model
{
    protected $connection = 'api_db';
    protected $table = 'preselected_adslots';
    protected $fillable = ['user_id', 'broadcaster_id', 'price', 'file_url', 'time', 'from_to_time', 'adslot_id',
                            'agency_id', 'filePosition_id', 'percentage', 'total_price', 'status',
                            'file_name', 'format', 'air_date'];
}
