<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $connection = 'api_db';
    protected $table = 'uploads';
    protected $fillable = ['user_id', 'time', 'file_url', 'file_name', 'channel', 'format', 'created_by'];
}
