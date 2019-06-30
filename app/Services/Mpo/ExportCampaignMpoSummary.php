<?php

namespace Vanguard\Services\Mpo;

use Vanguard\Services\Traits\GetDayPartTrait;
use PhpParser\Node\Expr\Cast\Object_;

class ExportCampaignMpoSummary
{
    protected $campaign_mpo_details;

    use GetDayPartTrait;

    public function __construct($campaign_mpo_details)
    {
        $this->campaign_mpo_details = $campaign_mpo_details;
    }

    public function run()
    {
        return $this->getSummaryData();
    }

    public function groupByDayPart()
    {
        return $this->campaign_mpo_details->map(function($item) {
            return $item->map(function($ads) {
                return collect($ads)->put('day_part', $this->getDayPart($ads->time_belt_start_time)['name']);
            })->groupBy('day_part');
        });
    }

    public function getSummaryData()
    {
        $summary = [];
        foreach($this->groupByDayPart() as $duration => $groupd_durations){
            foreach($groupd_durations as $day_part => $grouped_day_part){
                $summary[] = [
                    'duration' => $duration,
                    'day_part' => $day_part,
                    'total_spot' => $grouped_day_part->sum('ad_slots'),
                    'agency_percentage' => 15,
                    'volume_percent' => $grouped_day_part[0]['volume_discount'],
                    'total' => $grouped_day_part->sum('net_total')
                ];
            }
        }
        return $summary;
    }

}