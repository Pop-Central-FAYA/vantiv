<?php

namespace Vanguard\Services\Reports\Publisher\Month;

use \Vanguard\Libraries\MonthList;
use \Vanguard\Models\CampaignDetail;

use DB;
use Log;

/**
 * Get the campaigns launched monthly for all the months of the year
 */
class ActiveCampaigns
{
    const MONTHS_OF_THE_YEAR = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 
        'September', 'October', 'November', 'December');

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
     * select MONTH(time_created) as month_num, count(*) as num
     * from campaignDetails 
     * where time_created between '2019-01-01' and '2019-12-31' and status != 'on_hold' and launched_on in ('10zmij9sroads', '5c54a57939575', '5c653b68921a3', '5c653be378439')
     * group by MONTH(time_created)
     * order by month_num;
     */
    public function run()
    {
        $collection = CampaignDetail::selectRaw('MONTH(time_created) as month_num, count(*) as num')
            ->whereIn('launched_on', $this->company_id_list)
            ->when($this->filters, function($query) {
                foreach ($this->filters as $key => $value) {
                    switch ($key) {
                        case 'year':
                            $year_begin = "{$value}-01-01";
                            $year_end = "{$value}-12-31";
                            $query->whereBetween('time_created', [$year_begin, $year_end]);
                            break;
                        case 'station_id':
                            $query->whereIn('launched_on', $value);
                            break;
                        default:
                            break;
                    }
                }
            })
            // ->groupBy('MONTH(time_created)')
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
            $volume = 0;
            if ($month_val) {
                $volume = $month_val->num;
            }
            $values[] = $volume;
            $labels[] = $label;
        }
        return array(
            'labels' => $labels,
            'values' => $values
        );
    }

}
