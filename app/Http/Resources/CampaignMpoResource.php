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
            'adslots' => count(json_decode($this->adslots)),
            'net_total' => $this->net_total,
            'insertions' => $this->insertions,
            'email' => $this->vendor->contacts->where('is_primary', 1)[0]->email,
            'status' => $this->status,
            'reference' => $this->reference_number,
            'version' => $this->version,
            'is_recent' => $this->isRecentMpo(),
            'created_date' => date('Y-m-d', strtotime($this->created_at)),
            'links' => [
                'export' => route('mpos.export', ['mpo_id' => $this->id], true),
                'details' => route('mpos.details', ['mpo_id' => $this->id], true),
                'accept' => route('mpos.accept', ['mpo_id' => $this->id], true),
                'older_versions' => route('campaign.vendors.mpos.lists', ['campaign_id' => $this->campaign_id, 
                                                                        'ad_vendor_id' => $this->ad_vendor_id]) 
            ]
        ];
    }

    /**
     * Check if this mpo is the recent
     */
    private function isRecentMpo()
    {
        $mpo = $this->campaign->campaign_mpos
                    ->where('ad_vendor_id', $this->ad_vendor_id)
                    ->sortByDesc('created_at')
                    ->first();
        return $mpo->id === $this->id;
    }
}
