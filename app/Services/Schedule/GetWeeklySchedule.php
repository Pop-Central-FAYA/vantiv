<?php
namespace Vanguard\Services\Schedule;

use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Models\Schedule;
use Vanguard\Models\Company;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class GetWeeklySchedule
{
    protected $weekStartDate;
    protected $weekEndDate;
    protected $companyId;

    public function __construct($weekStartDate, $weekEndDate, $companyId)
    {
        $this->weekStartDate = $weekStartDate;
        $this->weekEndDate = $weekEndDate;
        $this->companyId = $companyId;
    }

    public function run()
    {
        return $this->formatScheduleData();
    }

    /**
     * get all schedule for the week from the database function
     *
     * @return collection
     */
    private function getWeekSchedule()
    {
        return TimeBeltTransaction::where('company_id', $this->companyId)
                    ->select(DB::raw("*, hour(playout_hour) AS hour, TIME_FORMAT(playout_hour, '%H:%i') AS ad_break"))
                    ->whereDate('playout_date', '>=', $this->weekStartDate)
                    ->whereDate('playout_date', '<=', $this->weekEndDate)
                    ->get();
    }

    private function formatScheduleData()
    {
        $grouped_schedule = [];
        $ad_pattern = Company::find($this->companyId)->publisher
                        ->decoded_settings['ad_pattern']['length'] === '4' ? '180 Seconds' : '240 Seconds';
        $schedules = $this->getWeekSchedule();
        foreach($schedules as $schedule){
            $grouped_schedule[] = [
                'playout_date' => $schedule->playout_date,
                'ad_pattern' => $ad_pattern,
                'program_name' => $schedule->media_program_name,
                'program_id' => $schedule->media_program_id,
                'background_color' => $this->generateColoeHex(), 
                'time_belt' => date('H:i', strtotime($schedule->time_belt->start_time)),
                'time_belt_trasaction_id' => $schedule->id,
                'campaign_name' => $schedule->campaign_details->name,
                'client_name' => $schedule->campaign_details->client->company_name,
                'duration' => $schedule->duration,
                'order' => $schedule->order
            ];
        }
        return $grouped_schedule;
    }  
    
    protected function generateColoeHex()
    {
        return '#' . substr(md5(mt_rand()), 0, 6);
    }
}