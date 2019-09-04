<?php

namespace Vanguard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vanguard\Services\Campaign\FormatCampaign;

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
        $format = new FormatCampaign();
        return [
            'id' =>  $this->campaign_reference,
            'campaign_id' =>  $this->id,
            'name' =>  $this->name,
            'product' =>  $this->product,
            'brand' => ucfirst($this->brand['name']),
            'campaign_reference' => $this->campaign_reference,
            'date_created' => date('Y-m-d', strtotime($this->time_created)),
            'start_date' => date('Y-m-d', strtotime($this->start_date)),
            'end_date' => date('Y-m-d', strtotime($this->stop_date)),
            'adslots' => $this->ad_slots,
            'budget' => number_format($this->budget,2),
            'status' => $format->getCampaignStatusHtml($this),
            'redirect_url' => $format->generateRedirectUrl($this),
            'station' => ''
        ];
    }
}