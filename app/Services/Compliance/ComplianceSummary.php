<?php

namespace Vanguard\Services\Compliance;

use Vanguard\Models\CampaignDetail;
use Vanguard\Models\SelectedAdslot;
use Vanguard\Services\Company\CompanyDetails;
use Maatwebsite\Excel\Facades\Excel;

class ComplianceSummary
{
    protected $campaign_id;
    protected $publisher_id;

    public function __construct($campaign_id, $publisher_id)
    {
        $this->campaign_id = $campaign_id;
        $this->publisher_id = $publisher_id;
    }

    public function campaignDetailsInformation()
    {
        return CampaignDetail::where('campaign_id', $this->campaign_id)->first();
    }

    public function getSelectedAdslots()
    {
        return SelectedAdslot::where('campaign_id', $this->campaign_id)
            ->when($this->publisher_id, function ($query) {
                $query->where('broadcaster_id', $this->publisher_id);
            })
            ->groupBy('adslot')
            ->get();
    }

    public function downloadComplianceSummary()
    {
        $compliance_reports = [];
        foreach ($this->getSelectedAdslots() as $schedule_slot){
            $broadcaster = new CompanyDetails($schedule_slot->broadcaster_id);
            $total_schedule_spot = $schedule_slot->countTotalSchedule($this->campaign_id, $schedule_slot->adslot);
            $total_campaign_to_time = $schedule_slot->countAiredSlots($schedule_slot->adslot, $this->campaign_id);
            $percent_compliance = $this->percentageCompliance($total_schedule_spot, $total_campaign_to_time);
            $compliance_reports[] = [
                'Station' => $broadcaster->getCompanyDetails()->name,
                'Adslot Information' => $schedule_slot->get_adslot->get_rate_card->hourly_range->time_range,
                'Total schedule spots' => $total_schedule_spot,
                'Total campaign to time' => $total_campaign_to_time,
                '% Compliance' =>   $percent_compliance .'%',
                'DS + CS' => $total_campaign_to_time,
                '% Compliance to total time' => $percent_compliance .'%',
                'Variance' => $total_schedule_spot - $total_campaign_to_time
            ];
        }

        $this->exportToExcel($compliance_reports, $this->campaignDetailsInformation()->name);
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
