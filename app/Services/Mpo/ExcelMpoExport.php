<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Services\BaseServiceInterface;
use Vanguard\Models\CampaignMpo;
use Vanguard\Exports\MpoExport;
use Illuminate\Support\Facades\DB;
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
        $mpo_details = CampaignMpo::with('campaign')->find($this->mpo_id);
        $company_logo = $mpo_details->campaign->company->logo;
        $campaign_mpo_time_belts = DB::table('campaign_mpo_time_belts')->select(DB::raw("*,
                                                        DATE_FORMAT(playout_date, '%Y-%m') AS month,
                                                        DATE_FORMAT(playout_date, '%d') AS day_number"))
                                                        ->where('mpo_id', $this->mpo_id)
                                                        ->get()
                                                        ->toArray();
        $campaign_mpo_time_belts = collect($campaign_mpo_time_belts);
        $days_array = [];
        for($i = 1; $i <=31; $i++){
            $days_array[] = $i;
        }
        $mpo_time_belts = new ExportCampaignMpo(
            $campaign_mpo_time_belts->groupBy(['program', 'duration'])        
        );
        $total_budget = $campaign_mpo_time_belts->sum('net_total');
        $net_total = $total_budget === 0 ? $total_budget : $total_budget - ((5/100)*$total_budget);
        $mpo_time_belt_summary = new ExportCampaignMpoSummary($campaign_mpo_time_belts->groupBy('duration'));
        return Excel::download(new MpoExport($mpo_time_belts->run(), 
                                $days_array, 
                                $mpo_details,
                                $total_budget,
                                $net_total,
                                $mpo_time_belt_summary->run(), $company_logo), str_slug($mpo_details->campaign->name).'.xlsx');
    }
}