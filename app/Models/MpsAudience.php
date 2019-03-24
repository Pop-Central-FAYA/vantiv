<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MspAudience extends Base
{
    protected $fillable = ['exact_age', 'gender', 'region', 'lsm'];
}