<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MpsAudienceProgramActivity extends Base
{
    protected $fillable = ['mps_audience_id', 'media_channel', 'station', 'program', 'start_time', 'end_time'];
}