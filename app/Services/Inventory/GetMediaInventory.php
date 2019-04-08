<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Services\Traits\CalculateTotalSlot;
use Vanguard\Services\Traits\FormatTimeBelt;
use Vanguard\Services\Traits\TimeBeltTrait;

class GetMediaInventory
{
    use TimeBeltTrait;
    use CalculateTotalSlot;
    use FormatTimeBelt;
    protected $station_id;

    public function __construct($station_id)
    {
        $this->station_id = $station_id;
    }

    private function mediaInventoryQuery()
    {
        return $this->baseQuery()
                    ->when(is_array($this->station_id), function ($query) {
                        return $query->whereIn('time_belts.station_id', $this->station_id);
                    })
                    ->when(!is_array($this->station_id), function ($query) {
                        return $query->where('time_belts.station_id', $this->station_id);
                    })
                    ->get();
    }

    public function getMediaInventory()
    {
        $media_inventory = [];
        foreach ($this->mediaInventoryQuery() as $inventory){
            $media_inventory[] = [
                'id' => $inventory->id,
                'time_belt' => $this->formatTimeBelt($inventory->start_time).'-'.$this->formatTimeBelt($inventory->end_time),
                'revenue' => 0,
                'day' => ucfirst($inventory->day),
                'rate_card' => $inventory->rate_card,
                'program' => $inventory->program_name,
                'station' => $inventory->station
            ];
        }
        return $media_inventory;
    }


}
