<?php

namespace Vanguard\Models;


class MpoDetail extends Base
{
    protected $table = 'mpoDetails';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'mpo_id', 'broadcaster_id', 'is_mpo_accepted', 'discount', 'status',
        'agency_id', 'agency_broadcaster'
    ];

    public $timestamps = false;
}
