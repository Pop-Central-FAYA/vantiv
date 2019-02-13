<?php

namespace Vanguard\Services\Adslot;

use Vanguard\Libraries\Utilities;
use Vanguard\Models\PreselectedAdslot;

class PreselectedAdslotService
{
    protected $user_id;
    protected $broadcaster_id;
    protected $agency_id;
    protected $request;
    protected $campaign_general_information;

    public function __construct($user_id, $broadcaster_id, $agency_id, $request, $campaign_general_information)
    {
        $this->user_id = $user_id;
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->request = $request;
        $this->campaign_general_information = $campaign_general_information;
    }

    public function getPreselectedSlots()
    {
        return Utilities::switch_db('api')->table('preselected_adslots')
                                    ->when($this->user_id, function ($query) {
                                        return $query->where('user_id', $this->user_id);
                                    })
                                    ->when($this->request && $this->request->adslot_id, function ($query) {
                                        return $query->where('adslot_id', $this->request->adslot_id);
                                    })
                                    ->when(!$this->request && $this->broadcaster_id, function($query) {
                                        return $query->where('broadcaster_id', $this->broadcaster_id);
                                    })
                                    ->when(!$this->request && $this->agency_id, function($query) {
                                        return $query->where('agency_id', $this->agency_id);
                                    })
                                    ->when($this->request && $this->broadcaster_id, function($query) {
                                        return $query->where('broadcaster_id', $this->broadcaster_id);
                                    })
                                    ->when($this->request && $this->request->position, function($query) {
                                        return $query->where('filePosition_id', $this->request->position);
                                    })
                                    ->when($this->request && $this->agency_id, function ($query) {
                                        return $query->where('agency_id', $this->agency_id);
                                    })
                                    ->get();
    }

    public function getSumPrice()
    {
        return Utilities::switch_db('api')->table('preselected_adslots')
                                                ->where('user_id', $this->user_id)
                                                ->sum('total_price');
    }

    public function storePreselectedAdslot()
    {
        $adslot_prices_object = new BroadcasterAdslotSurge($this->request->position, $this->request->price);
        $adslot_prices = $adslot_prices_object->calculateSurge();
        $adslot_position = new AdslotPositionService($this->broadcaster_id, $this->request->adslot_id, $this->request->position);
        $adslot_position->checkPositionAvailability();
        if((int)$this->request->position != ''){
            $adslot_position->reserveAdslotPosition();
        }
        if(count($this->getPreselectedSlots()) == 1){
            return 'error';
        }
        $total_spent = $this->getRecurrentTotal($adslot_prices);
        if($total_spent > (integer)$this->campaign_general_information->campaign_budget){
            return 'budget_exceed_error';
        }
        $preselected_adslot = new PreselectedAdslot();
        $preselected_adslot->user_id = $this->user_id;
        $preselected_adslot->broadcaster_id = $this->broadcaster_id;
        $preselected_adslot->price = $this->request->price;
        $preselected_adslot->file_url = $this->request->file;
        $preselected_adslot->from_to_time = $this->request->range;
        $preselected_adslot->time = $this->request->time;
        $preselected_adslot->adslot_id = $this->request->adslot_id;
        $preselected_adslot->percentage = $adslot_prices['percentage'];
        $preselected_adslot->total_price = $adslot_prices['new_price'];
        $preselected_adslot->filePosition_id = $this->request->position;
        $preselected_adslot->file_name = $this->request->file;
        $preselected_adslot->format = $this->request->file_format;
        $preselected_adslot->air_date = $this->request->air_date;
        $preselected_adslot->agency_id = $this->agency_id ? $this->agency_id : '';

        $preselected_adslot->save();

        return 'success';
    }

    public function getRecurrentTotal($adslot_prices)
    {
        $total_price = $this->sumTotalPriceByMediaBuyer();
        $new_total_price = (integer)$adslot_prices['new_price'] + $total_price;
        return $new_total_price;
    }

    public function sumTotalPriceByMediaBuyer()
    {
        return Utilities::switch_db('api')->table('preselected_adslots')
                                            ->when($this->broadcaster_id, function ($query) {
                                                return $query->where([
                                                    ['user_id', $this->user_id],
                                                    ['broadcaster_id', $this->broadcaster_id]
                                                ]);
                                            })
                                            ->when($this->agency_id, function ($query) {
                                                return $query->where([
                                                    ['user_id', $this->user_id],
                                                    ['agency_id', $this->agency_id]
                                                ]);
                                            })
                                            ->sum('total_price');

    }

    public function preselectedAdslotDetails()
    {
        return Utilities::switch_db('api')->table('preselected_adslots')
                        ->leftJoin('filePositions', 'filePositions.id', '=', 'preselected_adslots.filePosition_id')
                        ->join('companies', 'companies.id', '=', 'preselected_adslots.broadcaster_id')
                        ->select('preselected_adslots.id', 'preselected_adslots.from_to_time', 'preselected_adslots.time',
                            'preselected_adslots.price', 'preselected_adslots.percentage', 'preselected_adslots.total_price',
                            'preselected_adslots.air_date', 'filePositions.position', 'companies.name AS brand', 'companies.logo AS image_url'
                        )
                        ->where('preselected_adslots.user_id', $this->user_id)
                        ->when($this->broadcaster_id, function ($query) {
                            return $query->where('preselected_adslots.broadcaster_id', $this->broadcaster_id);
                        })
                        ->when($this->agency_id, function($query) {
                            return $query->where('preselected_adslots.agency_id', $this->agency_id);
                        })
                        ->get();
    }

    public function runPreselectedAdslotDetails()
    {
        $preselected_adslot_details = [];
        foreach ($this->preselectedAdslotDetails() as $preselectedAdslotDetail){
            $preselected_adslot_details[] = [
                'id' => $preselectedAdslotDetail->id,
                'from_to_time' => $preselectedAdslotDetail->from_to_time,
                'time' => $preselectedAdslotDetail->time,
                'price' => $preselectedAdslotDetail->price,
                'percentage' => $preselectedAdslotDetail->percentage,
                'position' => $preselectedAdslotDetail->position === null ? 'No Position' : $preselectedAdslotDetail->position,
                'total_price' => $preselectedAdslotDetail->total_price,
                'broadcaster_logo' => $preselectedAdslotDetail->image_url,
                'broadcaster_brand' => $preselectedAdslotDetail->brand,
                'air_date' => $preselectedAdslotDetail->air_date
            ];
        }
        return $preselected_adslot_details;
    }

    public function getAdslotIdFromPreselectedAdslot()
    {
        $adslot_ids = [];
        $preselected_adslots = $this->getPreselectedSlots();
        foreach ($preselected_adslots as $preselected_adslot){
            $adslot_ids[] = $preselected_adslot->adslot_id;
        }
        return $adslot_ids;
    }

    public function sumTotalPriceGroupedByBroadcaster()
    {
        return \DB::table('preselected_adslots')
            ->where('user_id', $this->user_id)
            ->when($this->broadcaster_id, function ($query) {
                return $query->groupBy('broadcaster_id');
            })
            ->when($this->agency_id, function($query) {
                return $query->groupBy('agency_id');
            })
            ->sum('total_price');

    }

    public function countPreselectedAdslot()
    {
        return count($this->getPreselectedSlots());
    }

    public function groupPreselectedAdslotByBoradscaster()
    {
        return Utilities::switch_db('api')->table('preselected_adslots')
                        ->selectRaw('SUM(total_price) AS total, COUNT(id) AS total_slot, broadcaster_id')
                        ->where([
                            ['user_id', $this->user_id],
                            ['agency_id', $this->agency_id]
                        ])
                        ->groupBy('broadcaster_id')
                        ->get();

    }

    public function groupSumDurationByAdslotId()
    {
        return Utilities::switch_db('api')->table('preselected_adslots')
                            ->select('adslot_id')
                            ->selectRaw('SUM(time) AS summed_time')
                            ->where('user_id', $this->user_id)
                            ->groupBy('adslot_id')
                            ->get();
    }
}
