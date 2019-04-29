<?php

namespace Vanguard\Services\Reports\Publisher\Month;

use \Vanguard\Libraries\MonthList;

use DB;
use Log;

/**
 * Get the monthly revenue of stations
 * Note, revenue is a two parter
 * 1. estimated revenue by month (this is the value of all the adslots booked for that month)
 * 2. actual revenue by month (this is the value of all the adslots that have aired for that month) 
 */
class StationRevenue
{

    protected $company_id_list;
    protected $filters;

    public function __construct($company_id_list)
    {
        $this->company_id_list = $company_id_list;
        $this->filters = array();
        return $this;
    }

    public function setFilters($filters) {
        $this->filters = $filters;
        return $this;
    }

    /**
     * select 
     * MONTH(tbt.playout_date) as month_num, 
     * SUM(tbt.amount_paid) as estimated_revenue,
     * SUM(IF(tbt.approval_status = 'approved', tbt.`amount_paid`, 0)) as actual_revenue 
     * from `campaignDetails` as `cd` 
     * inner join `time_belt_transactions` as `tbt` on `tbt`.`campaign_details_id` = `cd`.`id` 
     * where `cd`.`launched_on` in ('10zmij9sroads', '5c54a57939575', '5c653b68921a3', '5c653be378439') 
     * group by `month_num` 
     * order by `month_num` asc;
     */
    public function run()
    {
        $collection = DB::table('campaignDetails as cd')
            ->selectRaw('MONTH(tbt.playout_date) as month_num, SUM(tbt.amount_paid) as estimated_revenue, SUM(IF(tbt.approval_status = "approved", tbt.`amount_paid`, 0)) as actual_revenue')
            ->join('time_belt_transactions as tbt', 'tbt.campaign_details_id', '=', 'cd.id')
            ->whereIn('cd.launched_on', $this->company_id_list)
            ->when($this->filters, function($query) {
                foreach ($this->filters as $key => $value) {
                    switch ($key) {
                        case 'year':
                            $year_begin = "{$value}-01-01";
                            $year_end = "{$value}-12-31";
                            $query->whereBetween('tbt.playout_date', [$year_begin, $year_end]);
                            break;
                        case 'station_id':
                            $query->whereIn('cd.launched_on', $value);
                            break;
                        default:
                            break;
                    }
                }
            })
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get();
        
        return $this->formatCountsByMonth($collection);
    }

    protected function formatCountsByMonth($collection) {
        $labels = array();
        $estimated_value = array();
        $actual_value = array();
        $months = new MonthList();
        foreach ($months as $index => $label) {
            $month_val = $collection->firstWhere("month_num", $index);
            $actual_revenue = 0;
            $estimated_revenue = 0;
            if ($month_val) {
                $actual_revenue = (int) $month_val->actual_revenue;
                $estimated_revenue = (int) $month_val->estimated_revenue;
            }
            $estimated_value[] = $estimated_revenue;
            $actual_value[] = $actual_revenue;
            $labels[] = $label;
        }
        return array(
            'labels' => $labels,
            'estimated_value' => $estimated_value,
            'actual_value' => $actual_value
        );
    }

}
