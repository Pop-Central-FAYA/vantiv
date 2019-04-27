<?php

namespace Vanguard\Services\Reports\Publisher;

use DB;
use Log;

/**
 * Get number of mpos and their statuses by media type
 */
class MposByMediaType
{
    protected $company_id_list;

    public function __construct($company_id_list)
    {
        $this->company_id_list = $company_id_list;
        return $this;
    }

    public function run()
    {
        /**
         * @todo this is really bad, we need to rework how mpos are created and statuses maintained
         * select mpo_status, count(mpo_status) as num, type
         * from
         * (
         * select if(md.is_mpo_accepted = 0 and (cd.status != 'on_hold' or cd.status = 'file_error'), 'pending', 'accepted') as mpo_status, 'tv' as type
         * from companies as s 
         * join mpoDetails as md on md.broadcaster_id = s.id
         * join mpos m on m.id = md.mpo_id
         * join campaignDetails cd on cd.campaign_id = m.campaign_id and cd.launched_on = md.broadcaster_id
         * where s.id in ('10zmij9sroads', '5c54a57939575', '5c653b68921a3', '5c653be378439')
         * ) as mpo_list
         * group by mpo_status, type;
         */
        $inner_query = DB::table("companies AS s")
            ->selectRaw('IF(md.is_mpo_accepted = 0 and (cd.status != "on_hold" or cd.status = "file_error"), "pending", "accepted") AS status, "tv" as type')
            ->join('mpoDetails AS md', 'md.broadcaster_id', '=', 's.id')
            ->join('mpos AS m', 'm.id', '=', 'md.mpo_id')
            ->join('campaignDetails AS cd', function($join) {
                $join->on('cd.campaign_id', '=', 'm.campaign_id');
                $join->on('cd.launched_on', '=', 'md.broadcaster_id');
            })
            ->whereIn('s.id', $this->company_id_list);
        
        $collection = DB::query()->fromSub($inner_query, 'mpo_list')
            ->selectRaw('status, COUNT(status) AS num, type')
            ->groupBy('status', 'type')
            ->get();

        $grouped = $collection->groupBy('type');
        return $grouped->map(function ($item_list, $key) {
            return $this->formatItemList($item_list);
        });
    }

     /**
     * this will be an array like
     * [
     *      ['type' => 'tv', 'mpo_status' => 'accepted', 'num' => 16],
     *      ['type' => 'tv', 'mpo_status' => 'pending', 'num' => 41],
     * ]
     * convert to an assoc array like
     * [
     *      'accepted' => 16
     *      'pending' => 41
     * ]
     * there is a discrete list of statuses (those that are not present should have a value of 0)
     */
    protected function formatItemList($item_list) {
        $expected_statuses = $this->getMpoStatuses();
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

    protected function getMpoStatuses() {
        return array('pending', 'accepted');
    }
}
