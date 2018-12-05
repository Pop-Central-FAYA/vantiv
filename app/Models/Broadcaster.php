<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcaster extends Model
{
    protected $table = 'broadcasters';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'user_id', 'sector_id', 'sub_sector_id', 'nationality', 'location',
        'image_url', 'brand', 'status', 'channel_id'
    ];
}
