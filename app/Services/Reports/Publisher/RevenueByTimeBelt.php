<?php

namespace Vanguard\Services\Reports\Publisher;

use \Vanguard\Models\FakeTimeBeltRevenue;

use DB;
use Log;

class RevenueByTimeBelt
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

    public function __construct($company_id_list, $filters)
    {
        $this->company_id_list = $company_id_list;
        $this->filters = $filters;
        return $this;
    }

    public function run()
    {
        /**
         * select start_time, SUM(revenue) as revenue FROM fake_timebelt_revenue
         * WHERE (day = "") AND (station = "") AND (start_time BETWEEN (start_time, end_time))
         * GROUP BY start_time
         * ORDER BY start_time
         */
        $query = DB::table("time_belts as tb")
            ->selectRaw('tb.start_time, tb.end_time,  SUM(COALESCE(tbt.amount_paid, 0)) as revenue')
            ->leftJoin('time_belt_transactions as tbt', 'tbt.time_belt_id', '=', 'tb.id')
            ->whereIn('tb.station_id', $this->company_id_list)
            ->when($this->filters, function($query) {
                foreach($this->filters as $key => $value) {
                    $field = "tb." . $key;
                    if ($key == "day_parts") {
                        $query->whereBetween("start_time", static::DAYPARTS[$value]);
                        continue;
                    }
                    $query->where($field, $value);
                }
            })
            ->groupBy('tb.start_time', 'tb.end_time')
            ->orderBy('tb.start_time')
            ->get();

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
