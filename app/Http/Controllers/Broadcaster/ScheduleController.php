<?php

namespace Vanguard\Http\Controllers\Broadcaster;

use Vanguard\Services\Schedule\GetWeeklySchedule;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\Traits\SplitTimeRange;
use Illuminate\Support\Carbon;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use PhpOffice\PhpSpreadsheet\Chart\Exception;

class ScheduleController extends Controller
{
    /**
     * return array of time belts splited in 15 min
     */
    use SplitTimeRange;
    use CompanyIdTrait;

    /**
     * Pass details to the schedule weekly display view
     *
     * @return View
     */
    public function getWeeklySchedule()
    {
        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');
        $company_id = $this->companyId();
        $weeklySchedule = (new GetWeeklySchedule($weekStartDate, $weekEndDate, $company_id))->run();
        $time_belts = $this->splitTimeRangeByBase('00:00:00', '23:59:59', '15');
        return view('broadcaster_module.schedule.weekly_schedule')
                ->with('time_belts', $time_belts)
                ->with('schedules', $weeklySchedule); 
    }

    public function navigateWeekly(Request $request)
    {
        try {
            $company_id = $this->companyId();
            $weeklySchedule = (new GetWeeklySchedule($request->start_date, $request->end_date, $company_id))->run();
        }catch(Exception $exception){
            return response()->json([
                'status' => 'error',
                'message' => 'An error occured while performing your request'
            ]);
        } 
        return response()->json([
            'status' => 'success',
            'data' => $weeklySchedule
        ]);
    }
}
