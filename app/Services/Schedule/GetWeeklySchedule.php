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
    protected $ad_pattern;
    protected $selected_mpos;

    public function __construct($weekStartDate, $weekEndDate, $companyId, $ad_pattern, $selected_mpos)
    {
        $this->weekStartDate = $weekStartDate;
        $this->weekEndDate = $weekEndDate;
        $this->companyId = $companyId;
        $this->ad_pattern = $ad_pattern;
        $this->selected_mpos = $selected_mpos;
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
                    ->when($this->selected_mpos != null, function($query) {
                        return $query->whereIn('campaign_details_id', $this->selected_mpos);
                    })
                    ->select(DB::raw("*, hour(playout_hour) AS hour, TIME_FORMAT(playout_hour, '%H:%i') AS ad_break"))
                    ->whereDate('playout_date', '>=', $this->weekStartDate)
                    ->whereDate('playout_date', '<=', $this->weekEndDate)
                    ->get();
    }

    private function formatScheduleData()
    {
        $grouped_schedule = [];
        $schedules = $this->getWeekSchedule();
        foreach($schedules as $schedule){
            $grouped_schedule[] = [
                'playout_date' => $schedule->playout_date,
                'day' => strtolower(date('l', strtotime($schedule->playout_date))),
                'ad_pattern' => $this->ad_pattern,
                'program_name' => $schedule->media_program_name,
                'program_id' => $schedule->media_program_id,
                'background_color' => $this->generateColorHex($schedule->media_program_id), 
                'time_belt' => date('H:i', strtotime($schedule->time_belt->start_time)),
                'time_belt_trasaction_id' => $schedule->id,
                'campaign_name' => $schedule->campaign_details->name,
                'client_name' => $schedule->campaign_details->client->company_name,
                'duration' => $schedule->duration,
                'order' => $schedule->order,
                'hour' => $schedule->hour
            ];
        }
        return $grouped_schedule;
    }  
    
    protected function generateColorHex($media_program_id)
    {
        return '#' . substr(md5($media_program_id), 0, 6);
    }
}