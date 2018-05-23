<?php

namespace Vanguard\Http\Controllers\Clients;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Utilities;
use Yajra\Datatables\Datatables;

class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('clients.campaigns.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData(DataTables $dataTables, Request $request)
    {
        $campaign = [];
        $j = 1;
        $client_id = \Session::get('client_id');
        $user = Utilities::switch_db('api')->select("SELECT * from walkIns where id = '$client_id'");
        $user_id = $user[0]->user_id;
        if($request->start_date && $request->stop_date) {
            $start = date('Y-m-d', strtotime($request->start_date));
            $stop = date('Y-m-d', strtotime($request->stop_date));

            $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0 AND time_created BETWEEN '$start' AND '$stop' GROUP BY campaign_id ORDER BY time_created desc ");
            foreach ($all_campaign as $cam) {
                $today = date("Y-m-d");
                if (strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)) {
                    $status = 'Campaign Expired';
                } elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)) {
                    $status = 'Campaign In Progress';
                } else {
                    $now = strtotime($today);
                    $your_date = strtotime($cam->start_date);
                    $datediff = $your_date - $now;
                    $new_day =  round($datediff / (60 * 60 * 24));
                    $status = 'Campaign to start in '.$new_day.' day(s)';
                }
                $brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE id = '$cam->brand'");
                $pay = Utilities::switch_db('api')->select("SELECT total from payments WHERE campaign_id = '$cam->campaign_id'");
                $campaign[] = [
                    'id' => $j,
                    'camp_id' => $cam->id,
                    'name' => $cam->name,
                    'brand' => $brand[0]->name,
                    'product' => $cam->product,
                    'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                    'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                    'amount' => '&#8358;'.number_format($pay[0]->total, 2),
                    'status' => $status,
                    'campaign_id' => $cam->campaign_id,
                ];
                $j++;
            }
            return $dataTables->collection($campaign)
                ->addColumn('details', function ($campaign) {
                    return '<a href="' . route('client.campaign.details', ['id' => $campaign['camp_id']]) .'" class="btn btn-primary btn-xs" > Campaign Details </a>';
                })
                ->rawColumns(['details' => 'details'])->addIndexColumn()
                ->make(true);
        }

        $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE user_id = '$user_id' AND adslots > 0 GROUP BY campaign_id ORDER BY time_created desc ");
        foreach ($all_campaign as $cam) {
            $today = date("Y-m-d");
            if (strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)) {
                $status = 'Campaign Expired';
            } elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)) {
                $status = 'Campaign In Progress';
            } else {
                $now = strtotime($today);
                $your_date = strtotime($cam->start_date);
                $datediff = $your_date - $now;
                $new_day =  round($datediff / (60 * 60 * 24));
                $status = 'Campaign to start in '.$new_day.' day(s)';
            }
            $brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE id = '$cam->brand'");
            $pay = Utilities::switch_db('api')->select("SELECT total from payments WHERE campaign_id = '$cam->campaign_id'");
            $campaign[] = [
                'id' => $j,
                'camp_id' => $cam->id,
                'name' => $cam->name,
                'brand' => $brand[0]->name,
                'product' => $cam->product,
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'amount' => '&#8358;'.number_format($pay[0]->total, 2),
                'status' => $status,
                'campaign_id' => $cam->campaign_id,
            ];
            $j++;
        }
        return $dataTables->collection($campaign)
            ->addColumn('details', function ($campaign) {
                return '<a href="' . route('client.campaign.details', ['id' => $campaign['camp_id']]) .'" class="btn btn-primary btn-xs" > Campaign Details </a>';
            })
            ->rawColumns(['details' => 'details'])->addIndexColumn()
            ->make(true);
    }

    public function getDetails($id)
    {
        $campaign_details = Utilities::campaignDetails($id);
        return view('clients.campaigns.details', compact('campaign_details'));
    }

}
