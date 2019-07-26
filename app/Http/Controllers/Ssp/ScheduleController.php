<?php

namespace Vanguard\Http\Controllers\Ssp;

use Vanguard\Services\Schedule\GetWeeklySchedule;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\Traits\SplitTimeRange;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Chart\Exception;
use Vanguard\Models\Company;
use Auth;

class ScheduleController extends Controller
{
    /**
     * return array of time belts splited in 15 min
     */
    use SplitTimeRange;

    public function getWeeklySchedule(Company $company)
    {
        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');
        $ad_pattern = $company->user_company->publisher->decoded_settings['ad_pattern']['length'] === '4' ? '180' : '240';
        $weeklySchedule = (new GetWeeklySchedule($weekStartDate, $weekEndDate, $company->user_company->id, 
                            $ad_pattern))->run();
        $time_belts = $this->splitTimeRangeByBase('00:00:00', '23:59:59', '15');
        return view('broadcaster_module.schedule.weekly_schedule')
                ->with('time_belts', $time_belts)
                ->with('ad_pattern', $ad_pattern)
                ->with('schedules', collect($weeklySchedule)->groupBy('day')); 
    }

    public function navigateWeeklySchedule(Request $request, Company $company)
    {
        $ad_pattern = $company->user_company->publisher->decoded_settings['ad_pattern']['length'] === '4' ? '180' : '240';
        try {
            $weeklySchedule = (new GetWeeklySchedule($request->start_date, $request->end_date, 
                                $company->user_company->id, $ad_pattern))->run();
        }catch(Exception $exception){
            return response()->json([
                'status' => 'error',
                'message' => 'An error occured while performing your request'
            ]);
        } 
        return response()->json([
            'status' => 'success',
            'data' => collect($weeklySchedule)->groupBy('day')
        ]);
    }
}
