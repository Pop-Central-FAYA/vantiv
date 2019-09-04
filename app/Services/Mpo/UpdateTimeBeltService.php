<?php

namespace Vanguard\Services\Mpo;

use DB;
use Illuminate\Support\Arr;
use Vanguard\Models\CampaignMpoTimeBelt;
use Vanguard\Services\BaseServiceInterface;

class UpdateTimeBeltService implements BaseServiceInterface
{
    const CAMPAIGN_MPO_TIMEBELT_UPDATE_FIELDS = ['time_belt_start_time', 'day', 'duration', 'program', 'ad_slots',
                                                'playout_date','asset_id', 'volume_discount', 'net_total', 'unit_rate'];

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Signature method that gets called whenever this service is used
     *
     * @return void
     */
    public function run()
    {
        return DB::transaction(function() {
            $this->updateOtherColumns();
            $this->updateNetTotal();
        });
    }

    /**
     * This updates all the columns except the net_total
     *
     * @return void
     */
    protected function updateOtherColumns()
    {
        $data = $this->updateFields(self::CAMPAIGN_MPO_TIMEBELT_UPDATE_FIELDS, $this->data);
        return CampaignMpoTimeBelt::whereIn('id', $this->data['id'])
                                    ->update($data);
    }

    /**
     * This updates the net_total column
     *
     * @return void
     */
    protected function updateNetTotal()
    {
        return CampaignMpoTimeBelt::whereIn('id', $this->data['id'])
               ->update(['net_total' => DB::raw("(ad_slots * unit_rate) - ((volume_discount / 100) * (ad_slots * unit_rate))")]);
    }

    /**
     * Return a formatted data to be updated
     *
     * @param [type] $model
     * @param [type] $update_fields
     * @param [type] $data
     * @return Array
     */
    protected function updateFields($update_fields, $data)
    {
        $formatted_data = [];
        foreach ($update_fields as $key) {
            if (Arr::has($data, $key)) {
                $formatted_data[] = [
                    $key => $data[$key]
                ];
            }
        }
        return Arr::collapse($formatted_data);
    }
    
}