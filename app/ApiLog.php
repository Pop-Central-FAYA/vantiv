<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'api_logs';

    public $timestamps = false;
}