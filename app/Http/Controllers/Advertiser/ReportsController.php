<?php

namespace Vanguard\Http\Controllers\Advertiser;

use Session;
use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;

class ReportsController extends Controller
{
    public function index()
    {
        return view('advertisers.reports.index');
    }

    public function getCampaign(DataTables $dataTables, Request $request)
    {
        $advertiser = Session::get('advertiser_id');
        if($request->start_date && $request->stop_date){
            $start = date('Y-m-d', strtotime($request->start_date));
            $stop = date('Y-m-d', strtotime($request->stop_date));
            $campaign_report = [];
            $j = 1;
            $camp = Utilities::switch_db('api')->select("SELECT * from campaigns where user_id = '$advertiser' AND agency = '$advertiser' AND time_created BETWEEN '$start' AND '$stop' ORDER BY time_created desc");
            foreach ($camp as $campaign){
                $pay = Utilities::switch_db('api')->select("SELECT amount from payments where campaign_id = '$campaign->id'");
                $campaign_report[] = [
                    'id' => $j,
                    'campaign_name' => $campaign->name,
                    'start' => date('Y-m-d', strtotime($campaign->start_date)),
                    'stop' => date('Y-m-d', strtotime($campaign->stop_date)),
                    'amount' => '&#8358;'.number_format($pay[0]->amount, 2),
                ];
                $j++;
            }

            return $dataTables->collection($campaign_report)
                ->make(true);
        }

        $campaign_report = $this->dataTable($advertiser);
        return $dataTables->collection($campaign_report)
            ->make(true);


    }

    public function getRevenue(DataTables $dataTables, Request $request)
    {
        $advertiser = Session::get('advertiser_id');
        if($request->start_date && $request->stop_date){
            $start = date('Y-m-d', strtotime($request->start_date));
            $stop = date('Y-m-d', strtotime($request->stop_date));
            $campaign_report = [];
            $j = 1;
            $camp = Utilities::switch_db('api')->select("SELECT * from campaigns where user_id = '$advertiser' AND agency = '$advertiser' AND time_created BETWEEN '$start' AND '$stop' ORDER BY time_created desc");
            foreach ($camp as $campaign){
                $pay = Utilities::switch_db('api')->select("SELECT amount from payments where campaign_id = '$campaign->id'");
                $campaign_report[] = [
                    'id' => $j,
                    'date' => date('Y-m-d', strtotime($campaign->time_created)),
                    'campaign_name' => $campaign->name,
                    'amount' => '&#8358;'.number_format($pay[0]->amount, 2),
                ];
                $j++;
            }

            return $dataTables->collection($campaign_report)
                ->make(true);
        }

        $campaign_report = $this->dataTable($advertiser);
        return $dataTables->collection($campaign_report)
            ->make(true);

    }


    public function dataTable($user_id)
    {
        $advertiser = Session::get('advertiser_id');
        $campaign_report = [];
        $j = 1;
        $camp = Utilities::switch_db('api')->select("SELECT * from campaigns where user_id = '$user_id' AND agency = '$advertiser' ORDER BY time_created desc");
        foreach ($camp as $campaign){
            $pay = Utilities::switch_db('api')->select("SELECT amount from payments where campaign_id = '$campaign->id'");
            $campaign_report[] = [
                'id' => $j,
                'campaign_name' => $campaign->name,
                'start' => date('Y-m-d', strtotime($campaign->start_date)),
                'stop' => date('Y-m-d', strtotime($campaign->stop_date)),
                'amount' => '&#8358;'.number_format($pay[0]->amount, 2),
                'date' => date('Y-m-d', strtotime($campaign->time_created)),
            ];
            $j++;
        }

        return $campaign_report;
    }
}