<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class WalkIns extends Model
{
    protected $table = 'walkIns';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'user_id', 'broadcaster_id', 'status', 'nationality', 'location',
        'image_url', 'brand_id', 'client_type_id', 'agency_id', 'company_name','company_logo'
    ];
}
