<?php

namespace Vanguard\Services\Campaign;

class PeriodicRevenueChart
{

    protected $company_ids;

    public function __construct($company_ids)
    {
        $this->company_ids = $company_ids;
    }

    public function periodicRevenueQuery()
    {
        return \DB::table('campaignDetails')
            ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
            ->join('companies', 'companies.id', '=', 'campaignDetails.launched_on')
            ->join('paymentDetails', function ($query) {
                return $query->on('paymentDetails.payment_id', '=', 'payments.id')
                             ->on('paymentDetails.broadcaster', '=', 'campaignDetails.launched_on');
            })
            ->select('campaignDetails.launched_on AS company_id', 'companies.name AS station')
            ->selectRaw("MONTHNAME(campaignDetails.time_created) AS month,
                                    SUM(paymentDetails.amount) AS total")
            ->whereIn('campaignDetails.launched_on', $this->company_ids)
            ->groupBy(\DB::raw("MONTHNAME(campaignDetails.time_created),YEAR(campaignDetails.time_created), campaignDetails.launched_on"))
            ->get();
    }

    public function monthLists()
    {
        return [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];
    }

    public function periodicRevenueLogic()
    {

        $revenue_array = $this->periodicRevenueQuery();

        $revenue_company_group = [];
        $months = $this->monthLists();
        foreach($revenue_array as $key=>$value){
            $value = (array)$value;
            if(!array_key_exists($value['company_id'],$revenue_company_group)){
                $revenue_company_group[$value['company_id']] = $value;
                unset($revenue_company_group[$value['company_id']]['total']);
                unset($revenue_company_group[$value['company_id']]['month']);
                $revenue_company_group[$value['company_id']]['revenue_month'] =array();
                $revenue_company_group[$value['company_id']]['revenue_total'] =array();
                foreach ($months as $month){
                    if($month == $value['month']){
                        array_push($revenue_company_group[$value['company_id']]['revenue_total'],(integer)$value['total']);
                        array_push($revenue_company_group[$value['company_id']]['revenue_month'],$value['month']);
                    }else{
                        array_push($revenue_company_group[$value['company_id']]['revenue_total'],0);
                        array_push($revenue_company_group[$value['company_id']]['revenue_month'],$month);
                    }
                }
            }else
            {
                foreach ($months as $key => $month){
                    if($month == $value['month']){
                        $revenue_company_group[$value['company_id']]['revenue_total'][$key] = $value['total'];
                    }
                }

            }
        }
        return $revenue_company_group;
    }

    public function formatPeriodicChart()
    {
        $formatted_revenue_chart = [];
        $periodic_charts = $this->periodicRevenueLogic();
        foreach ($periodic_charts as $periodic_chart){
            $formatted_revenue_chart[] = [
                'name' => $periodic_chart['station'],
                'data' => $periodic_chart['revenue_total']
            ];
        }
        return ['formated_periodic_revenue_chart' => $formatted_revenue_chart,
                'month_list' => $this->monthLists()];
    }
}
