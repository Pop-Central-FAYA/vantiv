<?php

namespace Vanguard\Models;

class MediaPlanProgram extends Base
{
    protected $fillable = ['program_name', 'start_time', 'end_time', 'station', 'day'];
}
