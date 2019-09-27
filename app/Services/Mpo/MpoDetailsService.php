<?php

namespace Vanguard\Services\Mpo;

use Illuminate\Support\Arr;
use Vanguard\Services\BaseServiceInterface;
use Vanguard\Models\CampaignMpo;
use Vanguard\Libraries\DayPartList;
use Vanguard\Services\Traits\GetDayPartTrait;
use Riskihajar\Terbilang\Facades\Terbilang;

class MpoDetailsService implements BaseServiceInterface
{
    protected $mpo_id;

    use GetDayPartTrait;

    public function __construct($mpo_id)
    {
        $this->mpo_id = $mpo_id;
    }

    public function run()
    {
        $mpo_details = $this->getMpo();
        $campaign = $mpo_details->campaign;
        $company = $campaign->company;
        $mpo_array = $mpo_details->toArray();
        $previous_reference = $this->getPreviousMpo($campaign, $mpo_array, 'reference_number');
        $previous_mpo_id = $this->getPreviousMpo($campaign, $mpo_array, 'id');
        $campaign_mpo_time_belts = $this->getCampaignTimeBeltsCollection($mpo_details);
        $days_array = $this->makeDays();
        $mpo_time_belts = $this->getMpoTimeBelts($campaign_mpo_time_belts);
        $total_budget = $campaign_mpo_time_belts->sum('net_total');
        $agency_commission = $this->agencyCommission($total_budget);
        $net_total = $total_budget === 0 ? $total_budget : $total_budget - ((5/100)*$total_budget) - $agency_commission;
        $mpo_time_belt_summary = $this->getMpoSumary($campaign_mpo_time_belts);
        return [
            'time_belts' => $mpo_time_belts,
            'day_numbers' => $days_array,
            'mpo_details' => $mpo_details,
            'previous_reference' => $previous_reference,
            'previous_mpo' => route('mpos.details', ['mpo_id' => $previous_mpo_id]),
            'total_budget' => $total_budget,
            'net_total' => $net_total,
            'time_belt_summary' => $mpo_time_belt_summary,
            'company' => $company,
            'net_total_word' => $this->formatNetTotalToWord($net_total),
            'links' => [
                'export' => route('mpos.export', ['mpo_id' => $this->mpo_id], true),
                'details' => route('mpos.details', ['mpo_id' => $this->mpo_id], true),
                'accept' => route('mpos.accept', ['mpo_id' => $this->mpo_id], true),
                'campaign_details' => route('agency.campaign.details', ['campaign_id' => $campaign->id])
            ]
        ];
    }


    protected function makeDays()
    {
        $days_array = [];
        for($i = 1; $i <=31; $i++){
            $days_array[] = $i;
        }
        return $days_array;
    }

    protected function getCampaignTimeBeltsCollection($mpo_details)
    {
        $campaign_mpo_time_belts = json_decode($mpo_details->adslots, true);
        $campaign_time_belts_data = $this->formatTimeBelt($campaign_mpo_time_belts);
        return collect($campaign_time_belts_data);
    }

    protected function getMpoSumary($campaign_mpo_time_belts)
    {
        $time_belt_group = $campaign_mpo_time_belts->groupBy(['publisher_name', 'duration']);
        $summary_data = [];
        foreach($time_belt_group as $publisher => $campaign_mpo){
            foreach($campaign_mpo as $duration => $mpo){
                $net = $mpo->sum('net_total');
                $summary_data[] = [
                    'publisher_name' => $publisher,
                    'duration' => $duration,
                    'day_part' => $this->getDayPart($mpo[0]['time_belt_start_time'])['name'],
                    'total_spot' => $mpo->sum('ad_slots'),
                    'agency_percentage' => 15,
                    'agency_commission' => $commission = $this->agencyCommission($net),
                    'volume_percent' => $mpo[0]['volume_discount'],
                    'material_title' => $mpo[0]['media_asset'] ? $mpo[0]['media_asset']['file_name'] : '',
                    'total' => $net - $commission,
                    'year' => date('Y', strtotime($mpo[0]['playout_date']))
                ];
            }
        }
        return $summary_data;
    }

    protected function getMpo()
    {
        return CampaignMpo::with('campaign.client', 'campaign.brand', 'vendor', 'campaign.creator')->find($this->mpo_id);
    }

    protected function formatTimeBelt($campaign_mpo_time_belts)
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
                'day_number' => date('j', strtotime($time_belt['playout_date'])),
                'media_asset' => $time_belt['media_asset']
            ];
        }
        return $campaign_time_belts_data;
    }

    protected function getPreviousMpo($campaign, $mpo_details, $pluck_parameter)
    {
        $mpo = $campaign->campaign_mpos->where('ad_vendor_id', $mpo_details['ad_vendor_id'])
                                                    ->pluck($pluck_parameter)
                                                    ->toArray();
        //retrun previous mpo
        if (($key = array_search($mpo_details[$pluck_parameter], $mpo)) !== false) {
            if(isset($mpo[$key - 1])){
                return $mpo[$key - 1];
            }
        }
    }

    protected function getMpoTimeBelts($campaign_mpo_time_belts)
    {
        $mpos = [];
        $time_belt_group = $campaign_mpo_time_belts->groupBy(['publisher_name', 'program', 'duration']);
        foreach($this->groupByDayPart($time_belt_group) as $station => $station_time_belts){
            foreach($station_time_belts as $program => $time_belts){
                foreach($time_belts as $duration => $slots){
                    foreach($slots as $day_part => $ads){
                        foreach($ads as $month => $ad){
                            $mpos[] = [
                                'duration' => $duration,
                                'station' => $station,
                                'program' => $program,
                                'daypart' => $day_part,
                                'time_slot' => DayPartList::DAYPARTS[$day_part],
                                'day_range' => $this->daysRange($ad),
                                'month' => date('M y', strtotime($month)),
                                'slots' => $ad,
                                'exposures' => $this->pluckExposure($ad),
                                'total_slot' => $this->getTotalSlot($ad)
                            ];
                        }
                    }
                }
            }
        }
        return $mpos;
    }

    public function groupByDayPart($time_belt_group)
    {
        return $time_belt_group->map(function($station_item) {
            return $station_item->map(function($item) {
                return $item->map(function($ads) {
                    return $ads->map(function($ad) {
                        return collect($ad)->put('day_part', $this->getDayPart($ad['time_belt_start_time'])['name']);
                    })->groupBy(['day_part', 'month', 'playout_date']);  
                });
            });
        });
    }

    private function pluckExposure($ads)
    {
        return $ads->mapWithKeys(function ($item) {
            return [(int)$item[0]['day_number'] => $item->sum('ad_slots')];
        });
    }

    private function daysRange($ads)
    {
        $day_name = [];
        foreach($ads as $date => $ad){
            $day_name[] = date('D', strtotime($date));
        }
        if(current($day_name) != end($day_name)){
            return current($day_name) .' - '.end($day_name);
        }else{
            return current($day_name);
        }
    }

    public function getTotalSlot($ads)
    {
        $slot = 0;
        foreach($ads as $ad){
            $slot += $ad->sum('ad_slots');
        }
        return $slot;
    }

    private function agencyCommission($net_total)
    {
        return (15 / 100) * $net_total;
    }

    private function formatNetTotalToWord($net_total)
    {
        return Terbilang::make($net_total, ' Naira Only');
    }
}