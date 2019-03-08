<?php

namespace Vanguard\Services\Campaign;

class CampaignBudgetGraph
{
    protected $campaign_id;
    protected $media_publishers;

    public function __construct($campaign_id, $media_publishers)
    {
        $this->campaign_id = $campaign_id;
        $this->media_publishers = $media_publishers;
    }

    public function campaignBudgetGraphQueryForVerticalAxis($publisher_id)
    {
        return \DB::table('companies')
                    ->join('campaignDetails', 'campaignDetails.launched_on', '=', 'companies.id')
                    ->join('channel_company', 'channel_company.company_id', '=', 'companies.id')
                    ->join('campaignChannels', 'campaignChannels.id', '=', 'channel_company.channel_id')
                    ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
                    ->join('paymentDetails', function ($query) {
                        return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                                        ->on('paymentDetails.broadcaster', '=', 'companies.id');
                    })
                    ->select('companies.name AS publisher_name', 'paymentDetails.amount', 'campaignChannels.channel')
                    ->where([
                        ['companies.id', $publisher_id],
                        ['campaignDetails.campaign_id', $this->campaign_id],
                    ])
                    ->get();
    }

    public function campaignBudgetGraphQueryForHorizontalAxis()
    {
        return \DB::table('campaignDetails')
                    ->select('time_created')
                    ->where('campaign_id', $this->campaign_id)
                    ->groupBy(\DB::raw("DATE_FORMAT(time_created, '%Y-%m-%d')"))
                    ->get();
    }

    public function getCampaignBudgetDataForVerticalAxis()
    {
        $vertical_axis_data = [];
        foreach ($this->media_publishers as $media_publisher){
            $compliance_details = $this->campaignBudgetGraphQueryForVerticalAxis($media_publisher);
            if($compliance_details[0]->channel === 'TV'){
                $color = '#5281FE';
            }else{
                $color = '#00C4CA';
            }
            $vertical_axis_data[] = [
                'color' => $color,
                'name' => $compliance_details[0]->publisher_name,
                'data' => array($compliance_details[0]->amount),
                'stack' => $compliance_details[0]->channel
            ];
        }
        return $vertical_axis_data;
    }

    public function getCampaignBudgetDataForHorizontalAxis()
    {
        $horizontal_axis_data = [];
        foreach ($this->campaignBudgetGraphQueryForHorizontalAxis() as $date_compliance){
            $horizontal_axis_data[] = [date('Y-m-d', strtotime($date_compliance->time_created))];
        }
        return $horizontal_axis_data;
    }

    public function getCampaignBudgetData()
    {
        return response()->json(['data' => $this->getCampaignBudgetDataForVerticalAxis(),
                                'date' => $this->getCampaignBudgetDataForHorizontalAxis()]);
    }

}
