<?php

namespace Vanguard\Services\Reports\Publisher\Month;

use DB;
use Log;

class TimeBeltRevenue
{
    //todo need to use a filter class (since this is used by GetSuggestedPlans also)
    const DAYPARTS = array(
        "Late Night" => array("21:00", "23:59"),
        "Overnight" => array("00:00", "04:59"),
        "Breakfast" => array("05:00", "08:59"),
        "Late Breakfast" => array("09:00", "11:59"),
        "Afternoon" => array("12:00", "16:59"),
        "Primetime" => array("17:00", "20:59")
    );

    protected $filters;
    protected $company_id_list;

    public function __construct($company_id_list) {
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
        $query = DB::table("time_belts as tb")
            ->selectRaw('tb.start_time, tb.end_time,  SUM(COALESCE(tbt.amount_paid, 0)) as revenue')
            ->leftJoin('time_belt_transactions as tbt', function($join) {
                $join->on('tbt.time_belt_id', '=', 'tb.id');
                $join->where('tbt.approval_status', 'approved');
            })
            ->whereIn('tb.station_id', $this->company_id_list)
            ->when($this->filters, function($query) {
                foreach($this->filters as $key => $value) {
                    $field = "tb." . $key;
                    switch ($key) {
                        case 'day_parts':
                            $query->where(function($query) use ($value) {
                                $element = array_pop($value);
                                $query->whereBetween("tb.start_time", static::DAYPARTS[$element]);
                                while (count($value) > 0) {
                                    $element = array_pop($value);
                                    $query->orWhereBetween("tb.start_time", static::DAYPARTS[$element]);
                                }
                            });
                            break;
                        case 'station_id':
                            $query->whereIn($field, $value);
                            break;
                        case 'day':
                            $query->whereIn($field, $value);
                            break;
                        default:
                            break;
                    }
                }
            })
            ->groupBy('tb.start_time', 'tb.end_time')
            ->orderBy('tb.start_time')->get();
            
        $time_belts = $query->map(function($item) {
            $start_time = substr($item->start_time, 0, -3);
            $end_time = substr($item->end_time, 0, -3);
            return $start_time . '-' . $end_time;
        });

        $revenue = $query->map(function($item) {
            return (int) $item->revenue;
        });

        return array(
            "time_belts" => $time_belts,
            "revenue" => $revenue
        );
    }
}
