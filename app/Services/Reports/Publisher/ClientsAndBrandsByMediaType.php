<?php

namespace Vanguard\Services\Reports\Publisher;

use DB;
use Log;

/**
 * 1. Get the number of brands that have launched campaigns on this publisher grouped by type.
 * Change this once we have the different types
 * select count(*) as num, type
 * from
 * (
 * select
 * 'tv' as type, cd.brand
 * from companies c
 * join campaignDetails cd on cd.launched_on = c.id
 * where c.id in ('10zmij9sroads', '5c54a57939575', '5c653b68921a3', '5c653be378439')
 * ) as brand_list
 * group by type;

 */
class ClientsAndBrandsByMediaType
{
    protected $company_id_list;

    public function __construct($company_id_list)
    {
        $this->company_id_list = $company_id_list;
        return $this;
    }

    public function run()
    {
        return collect(array(
            "brands" => $this->getNumberOfActiveBrands(),
            "walkin_clients" => $this->getWalkinClientReports()
        ));
    }

    /**
     * this will be an array like
     * [
     *      'tv' => [num' => 16],
     *      'radio' => ['num' => 41]
     * ]
     */
    protected function getNumberOfActiveBrands() {
        $inner_query = DB::table("companies AS s")
            ->selectRaw('cd.brand, "tv" as type')
            ->join('campaignDetails AS cd', 'cd.launched_on', '=', 's.id')
            ->whereIn('s.id', $this->company_id_list);
        
        $collection = DB::query()->fromSub($inner_query, 'brand_list')
            ->selectRaw('COUNT(*) as num, type')
            ->groupBy('type')
            ->get();

        // add radio data (fake shit)
        $radio = (object) ['type' => 'radio', 'num' => 13];
        $collection->prepend($radio);

        $grouped = $collection->groupBy('type');
        return $grouped->map(function($item_list, $key) {
            return $item_list->first();
        });
    }

    /**
     * SAMPLE QUERY
     * select `name`, type, sum(IF(campaign_id is null, 0, 1)) as active_campaigns
     * from
     * (
     * select 
     * w.company_name as `name`, cd.id as campaign_id, 'tv' as type, w.id as walkin_id
     * from walkIns w
     * left join campaignDetails cd on cd.walkins_id = w.id
     * where w.broadcaster_id in ('10zmij9sroads', '5c54a57939575', '5c653b68921a3', '5c653be378439')
     * ) as walkin_list
     * group by walkin_id, type;
     * this will be an array like
     * [
     *      ['name' => 'WalkinOne', 'active_campaigns' => 16, 'type' => 'tv'],
     *      ['name' => 'WalkinOne', 'active_campaigns' => 0, 'type' => 'radio'],
     * ]
     */
    protected function getWalkinClientReports() {
        $inner_query = DB::table("walkIns AS w")
            ->selectRaw('w.company_name as name, cd.id as campaign_id, "tv" as type, w.id as walkin_id')
            ->leftJoin('campaignDetails AS cd', 'cd.walkins_id', '=', 'w.id')
            ->whereIn('w.broadcaster_id', $this->company_id_list);
        
        $collection = DB::query()->fromSub($inner_query, 'walkin_list')
            ->selectRaw('name, type, SUM(IF(campaign_id IS NULL, 0, 1)) as active_campaigns')
            ->groupBy('walkin_id', 'type')
            ->get();
        
        // add radio data (fake shit)
        $radio = (object) ['type' => 'radio', 'name' => 'Papa Ajasco', 'active_campaigns' => 2];
        $collection->prepend($radio);

        return $collection->groupBy('type');
    }
}
