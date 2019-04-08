<?php

namespace Vanguard\Services\Traits;

trait GetTimeBeltInventoryDetails
{
    use CalculateTotalSlot;

    public function getTimeBeltInventory($time_belts, $date)
    {
        $programs = [];
        foreach ($time_belts as $time_belt){
            $total_duration_sold = \DB::table('time_belt_transactions')
                ->where('time_belt_id', $time_belt->id)
                ->whereDate('playout_date', $date)
                ->groupBy('time_belt_id', 'playout_date')
                ->sum('duration');
            $programs[] = [
                'station' => $time_belt->station,
                'program_name' => $time_belt->program_name,
                'time_belt' => $time_belt->start_time.' '.$time_belt->end_time,
                'day' => $time_belt->day,
                'program_status' => $time_belt->status,
                'date' => $date,
                'total_slot_sold' => $total_duration_sold,
                'total_slot' => $total_slot = $this->calculateTotalSlot($time_belt->end_time, $time_belt->start_time),
                'total_slot_available' => $total_slot - $total_duration_sold
            ];
        }
        return $programs;
    }
}
