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
        $this->compareCampaignBudgetWithTotalSpent($adslot_prices);
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

    public function compareCampaignBudgetWithTotalSpent($adslot_prices)
    {
        $total_price = $this->sumTotalPriceByBroadcaster();
        $new_total_price = (integer)$adslot_prices['new_price'] + $total_price;
        if((integer)$new_total_price > (integer)$this->campaign_general_information->campaign_budget){
            return 'budget_exceed_error';
        }
    }

    public function sumTotalPriceByBroadcaster()
    {
        return Utilities::switch_db('api')->table('preselected_adslots')
                                            ->where([
                                                ['user_id', $this->user_id],
                                                ['broadcaster_id', $this->broadcaster_id]
                                            ])
                                            ->sum('total_price');

    }
}
