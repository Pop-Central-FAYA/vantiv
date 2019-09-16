<?php

namespace Vanguard\Services\Mpo;

use Illuminate\Support\Arr;
use Vanguard\Services\BaseServiceInterface;
use Vanguard\Models\CampaignMpo;
use Vanguard\Exports\MpoExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelMpoExport implements BaseServiceInterface
{
    protected $mpo_id;

    public function __construct($mpo_id)
    {
        $this->mpo_id = $mpo_id;
    }

    public function run()
    {
        $mpo_details = CampaignMpo::with('campaign', 'vendor')->find($this->mpo_id);
        $campaign = $mpo_details->campaign;
        $company_logo = $campaign->company->logo;
        $previous_reference = $this->getPreviousReference($campaign, $mpo_details);
        $campaign_mpo_time_belts = json_decode($mpo_details->adslots, true);
        $campaign_time_belts_data = $this->formatTimeBelt($campaign_mpo_time_belts);
        $campaign_mpo_time_belts = collect($campaign_time_belts_data);
        $days_array = [];
        for($i = 1; $i <=31; $i++){
            $days_array[] = $i;
        }
        $mpo_time_belts = new ExportCampaignMpo(
            $campaign_mpo_time_belts->groupBy(['publisher_name', 'program', 'duration'])        
        );
        $total_budget = $campaign_mpo_time_belts->sum('net_total');
        $net_total = $total_budget === 0 ? $total_budget : $total_budget - ((5/100)*$total_budget);
        $mpo_time_belt_summary = new ExportCampaignMpoSummary($campaign_mpo_time_belts->groupBy('duration'));
        return Excel::download(new MpoExport($mpo_time_belts->run(), 
                                $days_array, 
                                $mpo_details,
                                $previous_reference,
                                $total_budget,
                                $net_total,
                                $mpo_time_belt_summary->run(), $company_logo), 
                                str_slug($mpo_details->campaign->name).'_'.str_slug($mpo_details->vendor->name).'.xlsx');
    }

    private function formatTimeBelt($campaign_mpo_time_belts)
    {
        $campaign_time_belts_data = [];
        foreach($campaign_mpo_time_belts as $time_belt){
            $campaign_time_belts_data[] = [
                'id' => $time_belt['id'],
                'time_belt_start_time' => $time_belt['time_belt_start_time'],
                'time_belt_end_date' => $time_belt['time_belt_end_date'],
                'day' => $time_belt['day'],
                'duration' => $time_belt['duration'],
                'program' => $time_belt['program'],
                'ad_slots' => $time_belt['ad_slots'],
                'created_at' => $time_belt['created_at'],
                'playout_date' => $time_belt['playout_date'],
                'asset_id' => $time_belt['asset_id'],
                'volume_discount' => $time_belt['volume_discount'],
                'net_total' => $time_belt['net_total'],
                'unit_rate' => $time_belt['unit_rate'],
                'campaign_id' => $time_belt['campaign_id'],
                'publisher_id' => $time_belt['publisher_id'],
                'publisher_name' => $time_belt['publisher']['name'],
                'ad_vendor_id' => $time_belt['ad_vendor_id'],
                'month' => date('Y-m', strtotime($time_belt['playout_date'])),
                'day_number' => date('j', strtotime($time_belt['playout_date']))
            ];
        }
        return $campaign_time_belts_data;
    }

    private function getPreviousReference($campaign, $mpo_details)
    {
        $mpo_references = $campaign->campaign_mpos->where('ad_vendor_id', $mpo_details->ad_vendor_id)
                                                    ->pluck('reference_number')
                                                    ->toArray();
        //remove the current reference from the array
        if (($key = array_search($mpo_details->reference_number, $mpo_references)) !== false) {
            unset($mpo_references[$key]);
        }
        return Arr::last($mpo_references);
    }
}