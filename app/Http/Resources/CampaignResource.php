<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>  $this->id,
            'campaign_id' =>  $this->id,
            'name' =>  $this->name,
            'product' =>  $this->product,
            'brand' => ucfirst($this->brand->name),
            'campaign_reference' => $this->campaign_reference,
            'date_created' => date('Y-m-d', strtotime($this->time_created)),
            'start_date' => date('Y-m-d', strtotime($this->start_date)),
            'end_date' => date('Y-m-d', strtotime($this->stop_date)),
            'adslots' => $this->ad_slots,
            'budget' => $this->budget,
            'redirect_url' => route('agency.campaign.details', ['id' => $this->id], true),
            'media_type' => $this->media_type,
            'flight_date' => $this->flight_date,
            'created_at' => $this->created_at,
            'gender' => $this->gender,
            'status' => $this->status,
            'lsm' => $this->lsm,
            'social_class' => $this->social_class,
            'states' => $this->states,
            'regions' => $this->regions,
            'age_groups' => $this->age_groups,
            'grouped_time_belts' => $this->grouped_time_belts,
            'client' => $this->client,
            'creator' => $this->creator,
            'station' => '',
            'links' => [
                'mpos' => route('mpos.list', ['campaign_id' => $this->id], false),
                'update_adslots' => route('campaigns.adslots.update', ['campaign_id' => $this->id], false),
                'store_adslot' => route('campaigns.adslot.store', ['campaign_id' => $this->id]),
                'associate_assets' => route('campaigns.assets.associate', ['campaign_id' => $this->id], false),
                'store_campaign_mpo' => route('mpos.store', ['campaign_id' => $this->id], true)
            ]
        ];
    }
}