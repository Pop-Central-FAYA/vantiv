<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignMpoResource extends JsonResource
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
            'id' => $this->id,
            'vendor' => $this->vendor->name,
            'vendor_id' => $this->ad_vendor_id,
            'net_total' => $this->net_total,
            'insertions' => $this->insertions,
            'email' => $this->vendor->contacts->where('is_primary', 1)[0]->email,
            'export_url' => route('export.mpos', ['campaign_id' =>  $this->campaign_id, 'mpo_id' => $this->id])
        ];
    }
}
