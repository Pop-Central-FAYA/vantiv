<?php

namespace Vanguard\Services\Reports\Publisher;

use \Vanguard\Libraries\Enum\CampaignStatus;

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
class CampaignsByMediaType
{
    protected $company_id_list;

    public function __construct($company_id_list)
    {
        $this->company_id_list = $company_id_list;
    }

    public function run()
    {
        /**
         * @todo add media_type to the companies in order to group campaigns by media type
         * select cd.status, count(cd.status) as num, 'tv' as type
         * from companies as c 
         * join campaignDetails as cd on cd.launched_on = c.id
         * where c.id in ('10zmij9sroads', '5af99d3407617', '5af9a637e3b16', '5afaf21076fc6', '5b4ca11cc1d35', '5c54a57939575', '5c653b68921a3', '5c653be378439', '5c7d40f56ae77')
         * group by cd.status;
         */
        $collection = DB::table("companies as s")
            ->selectRaw('COUNT(cd.status) AS num, cd.status, "tv" as type')
            ->join('campaignDetails as cd', 'cd.launched_on', '=', 's.id')
            ->whereIn('s.id', $this->company_id_list)
            ->groupBy('cd.status', 'type')
            ->get();

        $grouped = $collection->groupBy('type');
        return $grouped->map(function ($item_list, $key) {
            return $this->formatItemList($item_list);
        });
    }

     /**
     * this will be an array like
     * [
     *      ['type' => 'tv', 'status' => 'active', 'num' => 16],
     *      ['type' => 'tv', 'status' => 'pending', 'num' => 5],
     *      ['type' => 'tv', 'status' => 'on_hold', 'num' => 33]
     * ]
     * convert to an assoc array like
     * [
     *      'active' => 16
     *      'pending' => 5
     *      'on_hold' => 33
     * ]
     * there is a discrete list of statuses (those that are not present should have a value of 0)
     */
    protected function formatItemList($item_list) {
        $expected_statuses = $this->getCampaignStatuses();
        $status_num = array();
        foreach ($expected_statuses as $status) {
            $item = $item_list->firstWhere('status', $status);
            $num = 0;
            if ($item !== null) {
                $num = $item->num;
            }
            $status_num[$status] = $num;
        }
        return $status_num;
    }

    protected function getCampaignStatuses() {
        return array(
            CampaignStatus::ACTIVE_CAMPAIGN,
            CampaignStatus::ON_HOLD,
            CampaignStatus::PENDING,
            CampaignStatus::FINISHED
        );
    }
}
