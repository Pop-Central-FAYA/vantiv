<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;
use Vanguard\User;

class Agency extends Model
{
    protected $table = 'agents';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'user_id', 'sector_id', 'sub_sector_id', 'nationality', 'location',
        'image_url', 'brand', 'status'
    ];

    //temporary and will be removed when the proper legal entity is implemented
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
