<?php

namespace Vanguard\Http\Controllers\Broadcaster;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function getWeeklySchedule()
    {
        return view('broadcaster_module.schedule.weekly_schedule');
    }
}
