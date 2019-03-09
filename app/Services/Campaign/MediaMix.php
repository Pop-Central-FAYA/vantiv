<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Models\CampaignChannel;
use Vanguard\Models\Payment;
use Vanguard\Services\Traits\ChartColor;

class MediaMix
{
    use ChartColor;
    protected $campaign_id;
    protected $channel;
    protected $publisher_retained;

    public function __construct($campaign_id, $channel, $publisher_retained)
    {
        $this->campaign_id = $campaign_id;
        $this->channel = $channel;
        $this->publisher_retained = $publisher_retained;
    }

    public function companyChannelsQuery()
    {
        return \DB::table('companies')
                    ->join('channel_company', 'channel_company.company_id', '=', 'companies.id')
                    ->join('campaignDetails', 'campaignDetails.launched_on', '=', 'companies.id')
                    ->select('companies.*')
                    ->whereIn('channel_company.channel_id', $this->channel)
                    ->where('campaignDetails.campaign_id', $this->campaign_id)
                    ->get();
    }

    public function getAllCompanyWithChannelInCampaign()
    {
        $all_company = [];
        foreach ($this->companyChannelsQuery() as $company){
            $all_company[] = [
                'broadcaster_id' => $company->id,
                'broadcaster' => $company->name,
                'campaign_id' => $this->campaign_id,
            ];
        }
        return $all_company;
    }

    public function getRetainedCompanyQuery()
    {
        return \DB::table('companies')
                    ->join('campaignDetails', 'campaignDetails.launched_on', '=', 'companies.id')
                    ->select('companies.name AS company_name', 'companies.id AS company_id')
                    ->where('campaignDetails.campaign_id', $this->campaign_id)
                    ->whereIn('companies.id', $this->publisher_retained)
                    ->get();
    }

    public function getRetainedCompany()
    {
        $retained_company = [];
        foreach ($this->getRetainedCompanyQuery() as $company){
            $retained_company[] = [
                'broadcaster_id' => $company->company_id,
                'broadcaster' => $company->company_name,
                'campaign_id' => $this->campaign_id,
            ];
        }
        return $retained_company;
    }

    public function getMediaMixData()
    {
        $media_mix_datas = [];
        foreach ($this->channel as $channel){
            $channel = CampaignChannel::where('id', $channel)->first();
            $payments = \DB::table('paymentDetails')
                            ->join('payments', 'payments.id', '=', 'paymentDetails.payment_id')
                            ->join('channel_company', 'channel_company.company_id', '=', 'paymentDetails.broadcaster')
                            ->where([
                                ['channel_company.channel_id', $channel->id],
                                ['payments.campaign_id', $this->campaign_id]
                            ])
                            ->sum('paymentDetails.amount');
            $total_amount = Payment::where('campaign_id', $this->campaign_id)->first();
            if($channel->channel === 'TV'){
                $color = $this->chartColors()->random();
            }else{
                $color = $this->chartColors()->random();
            }
            $media_mix_datas[] = [
                'name' => $channel->channel,
                'y' => (integer)(($payments / $total_amount->total) * 100),
                'color' => $color
            ];
        }
        return $media_mix_datas;
    }
}
