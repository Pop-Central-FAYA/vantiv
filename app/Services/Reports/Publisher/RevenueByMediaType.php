<?php

namespace Vanguard\Services\Reports\Publisher;

use DB;
use Log;

/**
 * Get Revenue by Media Type belonging to a certain publisher
 * So for instance, if the publisher has tv stations and radio stations
 * Get revenue and group by media type
 * @todo This should actually get the revenue from actual transactions that were purchased on the channel (read from timebelt transactions)
 * @todo We need to differentiate between realized revenue and estimated revenue (what the publisher actually showed to the audience vs what was purchased by the client)
 * SAMPLE QUERY
 * select sum(coalesce(tbt.amount_paid, 0)) as revenue, tb.station_id, s.name
 * from companies s
 * join time_belts tb on tb.station_id = s.id
 * left join time_belt_transactions tbt on tbt.time_belt_id = tb.id
 * where s.id in ('10zmij9sroads', '5af99d3407617', '5af9a637e3b16', '5afaf21076fc6', '5b4ca11cc1d35', '5c54a57939575', '5c653b68921a3', '5c653be378439', '5c7d40f56ae77') and tbt.approval_status = 'approved'
 * group by s.id
 * order by revenue desc;
 * @todo add media types to the stations
 */
class RevenueByMediaType
{
    protected $company_id_list;

    public function __construct($company_id_list)
    {
        $this->company_id_list = $company_id_list;
    }

    public function run()
    {
        /**
         * FAKE REVENUE QUERY
         * select sum(coalesce(ftbr.revenue, 0)) as revenue, s.id as station_id, s.name
         * from companies s
         * left join fake_time_belt_revenues ftbr on ftbr.station_id = s.id
         * where s.id in ('10zmij9sroads', '5af99d3407617', '5af9a637e3b16', '5afaf21076fc6', '5b4ca11cc1d35', '5c54a57939575', '5c653b68921a3', '5c653be378439', '5c7d40f56ae77')
         * group by s.id
         * order by revenue desc;
         */
        $collection = DB::table("companies as s")
            ->selectRaw('SUM(COALESCE(ftbr.revenue, 0)) AS revenue, s.id as station_id, s.name, "tv" as type')
            ->leftJoin('fake_time_belt_revenues as ftbr', 'ftbr.station_id', '=', 's.id')
            ->whereIn('s.id', $this->company_id_list)
            ->groupBy('s.id')
            ->get();

        $grouped = $collection->groupBy('type');
        
        $data = $grouped->map(function ($item_list, $key) {
            $sorted = $item_list->sortByDesc('revenue');
            $item = $sorted->first();
            $item->revenue = (int) $item->revenue;
            return $item;
        });

        return $data->values();
    }
}
