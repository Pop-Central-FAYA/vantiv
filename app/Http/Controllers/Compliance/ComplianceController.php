<?php

namespace Vanguard\Http\Controllers\Compliance;

use Maatwebsite\Excel\Facades\Excel;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\SelectedAdslot;

class ComplianceController extends Controller
{
    public function downloadSummary($campaign_id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $campaign_information = Utilities::switch_db('api')->select("
                                    SELECT * FROM campaignDetails 
                                    WHERE campaign_id = '$campaign_id'
                                ");

        if($broadcaster_id){
            $schedule_slots = SelectedAdslot::where([
                    ['campaign_id', $campaign_id],
                    ['broadcaster_id', $broadcaster_id]
                ])
                ->groupBy('adslot')
                ->get();
        }else{
            $schedule_slots = SelectedAdslot::where('campaign_id', $campaign_id)
                ->groupBy('adslot')
                ->get();
        }

        $compliance_reports = [];
        foreach ($schedule_slots as $schedule_slot){
            $broadcaster = Utilities::getBroadcasterDetails($schedule_slot->broadcaster_id);
            $total_schedule_spot = $schedule_slot->countTotalSchedule($campaign_id, $schedule_slot->adslot);
            $total_campaign_to_time = $schedule_slot->countAiredSlots($schedule_slot->adslot, $campaign_id);
            $percent_compliance = $this->percentageCompliance($total_schedule_spot, $total_campaign_to_time);
            $compliance_reports[] = [
                'Station' => $broadcaster[0]->brand,
                'Adslot Information' => $schedule_slot->get_adslot->get_rate_card->hourly_range->time_range,
                'Total schedule spots' => $total_schedule_spot,
                'Total campaign to time' => $total_campaign_to_time,
                '% Compliance' =>   $percent_compliance .'%',
                'DS + CS' => $total_campaign_to_time,
                '% Compliance to total time' => $percent_compliance .'%',
                'Variance' => $total_schedule_spot - $total_campaign_to_time
            ];
        }

        $this->exportToExcel($compliance_reports, $campaign_information[0]->name);

    }

    public function percentageCompliance($total_schedule_spot, $total_campaign_to_time)
    {
        return ($total_campaign_to_time / $total_schedule_spot) * 100;
    }

    public function exportToExcel($compliance_reports, $campaign_name)
    {
        return Excel::create($campaign_name, function($excel) use ($compliance_reports, $campaign_name) {

                    $excel->sheet($campaign_name, function($sheet) use ($compliance_reports) {

                        $sheet->fromArray(
                            $compliance_reports, null, 'A1', true
                        );

                    });

                })->export('xlsx');
    }
}
