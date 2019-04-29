<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class FakeTimeBeltRevenue extends Base
{
    protected $fillable = ['id', 'station_id', 'day', 'start_time', 'end_time', 'revenue'];
}
