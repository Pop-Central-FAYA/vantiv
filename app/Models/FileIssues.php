<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class FileIssues extends Model
{
    protected $connection = 'api_db';
    protected $fillable = ['name'];
}
