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
            'vendor' => $this->ad_vendor_id ? $this->vendor->name : '',
            'publisher' => $this->publisher_id ? $this->publisher->name : '',
            'vendor_id' => $this->ad_vendor_id,
            'adslots' => count(json_decode($this->adslots)),
            'net_total' => $this->net_total,
            'insertions' => $this->insertions,
            'email' => $this->ad_vendor_id ? $this->vendor->contacts->where('is_primary', 1)[0]->email : '',
            'status' => $this->status,
            'reference' => $this->reference_number,
            'version' => $this->version,
            'is_recent' => $this->isRecentMpo(),
            'campaign' => $this->campaign,
            'created_date' => date('Y-m-d', strtotime($this->created_at)),
            'links' => [
                'export' => route('mpos.export', ['mpo_id' => $this->id], false),
                'details' => route('mpos.details', ['mpo_id' => $this->id], false),
                'accept' => route('mpos.accept', ['mpo_id' => $this->id], false),
                'user_list' => route('mpos.permitted_users', ['mpo_id' => $this->id], false),
                'request_approval' => route('mpos.request_approval', ['mpo_id' => $this->id], true),
                'store_share_links' => route('mpo_share_link.store', ['mpo_id' => $this->id], false),
                'submit_to_vendor' => route('mpo_share_link.submit', ['mpo_id' => $this->id], false),
                'active_share_link' => route('mpos.active_link', ['mpo_id' => $this->id], false)
            ]
        ];
    }

    /**
     * Check if this mpo is the recent
     */
    private function isRecentMpo()
    {
        $mpo = $this->campaign->campaign_mpos
                    ->when($this->publisher_id, function($query) {
                        return $query->where('publisher_id', $this->publisher_id);
                    })
                    ->when($this->ad_vendor_id, function($query) {
                        return $query->where('ad_vendor_id', $this->ad_vendor_id);
                    })
                    ->sortByDesc('created_at')
                    ->first();
        return $mpo->id === $this->id;
    }
}
