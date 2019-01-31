<?php

namespace Vanguard\Models;


class WalkIns extends Base
{
    protected $table = 'walkIns';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'user_id', 'broadcaster_id', 'status', 'nationality', 'location',
        'image_url', 'brand_id', 'client_type_id', 'agency_id', 'company_name','company_logo'
    ];
}
