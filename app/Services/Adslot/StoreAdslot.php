<?php

namespace Vanguard\Services\Adslot;

use Vanguard\Models\Adslot;

class StoreAdslot
{
    protected $ratecard_id;
    protected $target_audience_id;
    protected $daypart_id;
    protected $region_id;
    protected $from_to_time;
    protected $min_age;
    protected $max_age;
    protected $company_id;
    protected $time_difference;
    protected $channel_id;

    public function __construct($ratecard_id, $target_audience_id, $daypart_id, $region_id, $from_to_time, $min_age,
                                $max_age, $company_id, $time_difference, $channel_id)
    {
        $this->ratecard_id = $ratecard_id;
        $this->target_audience_id = $target_audience_id;
        $this->daypart_id = $daypart_id;
        $this->region_id = $region_id;
        $this->from_to_time = $from_to_time;
        $this->min_age = $min_age;
        $this->max_age = $max_age;
        $this->company_id = $company_id;
        $this->time_difference = $time_difference;
        $this->channel_id = $channel_id;
    }

    public function storeAdslot()
    {
        $adslot = new Adslot();
        $adslot->rate_card = $this->ratecard_id;
        $adslot->target_audience = $this->target_audience_id;
        $adslot->day_parts = $this->daypart_id;
        $adslot->region = $this->region_id;
        $adslot->from_to_time = $this->from_to_time;
        $adslot->min_age = $this->min_age;
        $adslot->max_age = $this->max_age;
        $adslot->broadcaster = $this->company_id;
        $adslot->is_available = 0;
        $adslot->time_difference = $this->time_difference;
        $adslot->time_used = 0;
        $adslot->channels = $this->channel_id;
        $adslot->company_id = $this->company_id;
        $adslot->save();
        return $adslot;
    }
}
