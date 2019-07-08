<?php

namespace Vanguard\Services\Mpo;

use Illuminate\Support\Facades\DB;

class CreateMpoTimeBelt
{
    protected $request;
    protected $mpo_id;
    protected $duration;

    public function __construct($request, $mpo_id, $duration)
    {
        $this->request = $request;
        $this->mpo_id = $mpo_id;
        $this->duration = $duration;
    }

    public function run()
    {
        return DB::table('campaign_mpo_time_belts')->insert($this->getDataToCreate());
    }

    private function getDataToCreate()
    {
        $mpo_time_belts = [];
        foreach($this->request->time_belts as $key => $time_belt){
            $mpo_time_belts[] = [
                'mpo_id' => $this->mpo_id,
                'time_belt_start_time' => $time_belt['time_belt'],
                'time_belt_end_date' => $time_belt['time_belt'],
                'day' => date('l', strtotime($this->request->playout_date)),
                'duration' => $this->duration,
                'program' => $this->request->program,
                'ad_slots' => $insertion = $this->request->insertions[$key]['insertion'],
                'created_at' => now(),
                'updated_at' => now(),
                'playout_date' => $this->request->playout_date,
                'asset_id' => $this->request->asset_id,
                'volume_discount' => $volume_disc = $this->request->volume_discount,
                'net_total' => $this->calculateNetTotal($insertion, $this->request->unit_rate, $volume_disc),
                'unit_rate' => $this->request->unit_rate
            ];
        }
        return $mpo_time_belts;
    }

    private function calculateNetTotal($insertion, $unit_rate, $volume_discount)
    {
        $gross_total = $insertion * $unit_rate;
        $volume_amount = ($volume_discount / 100) * $gross_total;
        return ceil($gross_total - $volume_amount);
    }
}