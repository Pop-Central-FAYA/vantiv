<?php

namespace Vanguard\Http\Controllers\Agency;

use Session;
use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;

class ReportsController extends Controller
{
    public function index()
    {
        $agency_id = Session::get('agency_id');
        $user_d = [];
        $user = Utilities::switch_db('api')->select("SELECT user_id from walkIns where agency_id = '$agency_id'");
        foreach ($user as $u){
            $user_det = \DB::select("SELECT * from users where id = '$u->user_id'");
            $user_d[] = [
                'user_id' => $u->user_id,
                'name' => $user_det[0]->first_name.' '.$user_det[0]->last_name,
            ];
        }

        return view('agency.reports.index')->with('user', $user_d);
    }

    public function getCampaign(DataTables $dataTables, Request $request)
    {
        $agency_id = Session::get('agency_id');
        if($request->client){
            if($request->start_date && $request->stop_date){
                $start = date('Y-m-d', strtotime($request->start_date));
                $stop = date('Y-m-d', strtotime($request->stop_date));
                $campaign_report = [];
                $j = 1;
                $camp = Utilities::switch_db('api')->select("SELECT * from campaigns where user_id = '$request->client' AND agency = '$agency_id' AND time_created BETWEEN '$start' AND '$stop' ORDER BY time_created desc");
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

            $campaign_report = $this->dataTable($request->client);
            return $dataTables->collection($campaign_report)
                ->make(true);
        }
        $user_re_id = 0;

        $campaign_report = $this->dataTable($user_re_id);
        return $dataTables->collection($campaign_report)
            ->make(true);

    }

    public function getRevenue(DataTables $dataTables, Request $request)
    {
        $agency_id = Session::get('agency_id');
        if($request->client){
            if($request->start_date && $request->stop_date){
                $start = date('Y-m-d', strtotime($request->start_date));
                $stop = date('Y-m-d', strtotime($request->stop_date));
                $campaign_report = [];
                $j = 1;
                $camp = Utilities::switch_db('api')->select("SELECT * from campaigns where user_id = '$request->client' AND agency = '$agency_id' AND time_created BETWEEN '$start' AND '$stop' ORDER BY time_created desc");
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

            $campaign_report = $this->dataTable($request->client);
            return $dataTables->collection($campaign_report)
                ->make(true);
        }
        $user_re_id = 0;

        $campaign_report = $this->dataTable($user_re_id);
        return $dataTables->collection($campaign_report)
            ->make(true);

    }


    public function dataTable($user_id)
    {
        $agency_id = Session::get('agency_id');
        $campaign_report = [];
        $j = 1;
        $camp = Utilities::switch_db('api')->select("SELECT * from campaigns where user_id = '$user_id' AND agency = '$agency_id' ORDER BY time_created desc");
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