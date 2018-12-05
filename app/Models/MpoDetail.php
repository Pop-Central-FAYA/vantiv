<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MpoDetail extends Model
{
    protected $table = 'mpoDetails';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'mpo_id', 'broadcaster_id', 'is_mpo_accepted', 'discount', 'status',
        'agency_id', 'agency_broadcaster'
    ];
}
