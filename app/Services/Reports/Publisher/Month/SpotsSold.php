<?php

namespace Vanguard\Services\Reports\Publisher\Month;

use \Vanguard\Libraries\MonthList;

use DB;
use Log;

/**
 * Get the number of spots sold monthly
 * @todo Use proper precision (Decimal class etc)
 */
class SpotsSold
{

    const DAILY_SPOT_SECONDS = 17280;

    const MONTHLY_SPOT_SECONDS = array(
        'January' => 535680,
        'February' => 483840,
        'March' => 535680,
        'April' => 518400,
        'May' => 535680,
        'June' => 518400,
        'July' => 535680,
        'August' => 535680,
        'September' => 518400,
        'October' => 535680,
        'November' => 518400,
        'December' => 535680,
    );
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

    public function run()
    {
        $collection = DB::table('campaignDetails as cd')
            ->selectRaw('MONTH(tbt.playout_date) as month_num, SUM(tbt.duration) as num')
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
        $values = array();
        $months = new MonthList();
        foreach ($months as $index => $label) {
            $month_val = $collection->firstWhere("month_num", $index);
            $spots_sold = 0;
            if ($month_val) {
                $spots_sold = $month_val->num;
            }

            $total = static::MONTHLY_SPOT_SECONDS[$label];
            $percentage = round(($spots_sold/$total) * 100, 2);
            $values[] = $percentage;
            $labels[] = $label;
        }
        return array(
            'labels' => $labels,
            'values' => $values
        );
    }

}
