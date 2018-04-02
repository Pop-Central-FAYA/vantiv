<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Api;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;

class MpoController extends Controller
{
    public function index()
    {
        $broadcaster_id = \Session::get('broadcaster_id');

        $mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpos WHERE broadcaster_id = '$broadcaster_id' OR agency_broadcaster = '$broadcaster_id' ORDER BY time_created DESC ");

        $mpo_data = [];

        foreach ($mpos as $mpo) {

            $campaign_details = Api::fetchCampaign($mpo->campaign_id);
            $brand = Api::brand($mpo->campaign_id);
            $payment_details = Api::fetchPayment($mpo->campaign_id);
            $status = Api::approvedCampaignFiles($mpo->campaign_id);

            if (count($campaign_details) === 0) {
                $product = 0;
                $name = 0;
                $time = 0;
            } else {
                $product = $campaign_details[0]->product;
                $name = $campaign_details[0]->name;
                $time = date('Y-m-d', strtotime($campaign_details[0]->time_created));
            }

            if (count($payment_details) === 0) {
                $amount = 0;
            } else {
                $amount = $payment_details[0]->amount;
            }

            $mpo_data[] = [
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $product,
                'amount' => $amount,
                'name' => $name,
                'brand' => $brand[0]->name,
                'time_created' => $time,
                'status' => $status
            ];
        }

        return view('mpos.index', compact('mpo_data'));
    }

    public function pending_mpos_data(DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');

        $pending_mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpos WHERE is_mpo_accepted = 0 AND (broadcaster_id = '$broadcaster_id' OR agency_broadcaster = '$broadcaster_id') ORDER BY time_created DESC ");

        $mpo_data = [];
        $j = 1;

        foreach ($pending_mpos as $mpo) {

            if (Api::pendingMPOs($mpo) === true) {

                $campaign_details = Api::fetchCampaign($mpo->campaign_id);
                $payment_details = Api::fetchPayment($mpo->campaign_id);

                if (count($campaign_details) === 0) {
                    $product = 0;
                    $brand = 0;
                    $name = 0;
                    $time = 0;
                    $start_date = 0;
                    $start_end = 0;
                    $channel = 0;
                } else {
                    $product = $campaign_details[0]->product;
                    $brand = Api::brand($mpo->campaign_id);
                    $name = $campaign_details[0]->name;
                    $time = date('Y-m-d', strtotime($campaign_details[0]->time_created));
                    $start_date = $campaign_details[0]->start_date;
                    $start_end = $campaign_details[0]->stop_date;
                    $channel = Api::getChannelName($campaign_details[0]->channel)[0]->channel;
                }

                if (count($payment_details) === 0) {
                    $amount = 0;
                } else {
                    $amount = $payment_details[0]->amount;
                }

                if (Api::getOutstandingFiles($mpo->campaign_id) === 0) {
                    $files = 0;
                } else {
                    $files = Api::getOutstandingFiles($mpo->campaign_id);
                }

                $mpo_data[] = [
                    's_n' => $j,
                    'id' => $mpo->id,
                    'is_mpo_accepted' => $mpo->is_mpo_accepted,
                    'product' => $product,
                    'brand' => $brand[0]->name,
                    'campaign_name' => $name,
                    'channel' => $channel,
                    'time_created' => $time,
                    'start_date' => date('M j, Y h:ia', strtotime($start_date)),
                    'stop_date' => date('M j, Y h:ia', strtotime($start_end)),
                    'files' => $files,
                    'amount' => $amount
                ];
                $j++;
            }


        }

        return $dataTables->collection($mpo_data)
            ->addColumn('view', function ($mpo_data) {
                return '
                        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal' . $mpo_data['id'] . '" style="font-size: 16px">
                            View
                        </button>
                    ';
            })
            ->rawColumns(['view' => 'view'])->addIndexColumn()
            ->make(true);
    }

    public function pending_mpos()
    {
        $broadcaster_id = \Session::get('broadcaster_id');

        $pending_mpos = $mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpos WHERE is_mpo_accepted = 0 AND (broadcaster_id = '$broadcaster_id' OR agency_broadcaster = '$broadcaster_id') ORDER BY time_created DESC ");

        $mpo_data = [];

        foreach ($pending_mpos as $mpo) {

            $campaign_details = Api::fetchCampaign($mpo->campaign_id);
            $payment_details = Api::fetchPayment($mpo->campaign_id);

            if (count($campaign_details) === 0) {
                $product = 0;
                $brand = 0;
                $name = 0;
                $time = 0;
                $start_date = 0;
                $start_end = 0;
                $channel = 0;
            } else {
                $product = $campaign_details[0]->product;
                $brand = Api::brand($mpo->campaign_id);
                $name = $campaign_details[0]->name;
                $time = date('Y-m-d', strtotime($campaign_details[0]->time_created));
                $start_date = $campaign_details[0]->start_date;
                $start_end = $campaign_details[0]->stop_date;
                $channel = Api::getChannelName($campaign_details[0]->channel)[0]->channel;
            }

            if (count($payment_details) === 0) {
                $amount = 0;
            } else {
                $amount = $payment_details[0]->amount;
            }

            if (Api::getOutstandingFiles($mpo->campaign_id) === 0) {
                $files = 0;
            } else {
                $files = Api::getOutstandingFiles($mpo->campaign_id);
            }

            $mpo_data[] = [
                'id' => $mpo->id,
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $product,
                'brand' => $brand[0]->name,
                'campaign_name' => $name,
                'channel' => $channel,
                'time_created' => $time,
                'start_date' => $start_date,
                'stop_date' => $start_end,
                'files' => $files,
                'amount' => $amount
            ];
        }

        return view('mpos.pending-mpos', compact('mpo_data'));
    }

    public function update_file($is_file_accepted, $file_code, $rejection_reason)
    {
        if (request()->ajax()) {

            if ($is_file_accepted !== 'null' && $rejection_reason === 'null') {
                $update_file = Utilities::switch_db('reports')->select("UPDATE files SET is_file_accepted = '$is_file_accepted' WHERE file_code = '$file_code'");
            }

            if ($is_file_accepted === 'null' && $rejection_reason !== 'null') {
                $update_file = Utilities::switch_db('reports')->select("UPDATE files SET rejection_reason = '$rejection_reason' WHERE file_code = '$file_code'");
            }

            if ($is_file_accepted !== 'null' && $rejection_reason !== 'null') {
                $update_file = Utilities::switch_db('reports')->select("UPDATE files SET is_file_accepted = '$is_file_accepted', rejection_reason = '$rejection_reason' WHERE file_code = '$file_code'");
            }

            //api call
            if($is_file_accepted === "1"){
                $add = Api::addFilesToApi($file_code);
                if ($add->file_code === $file_code) {
                    $insertStatus = [
                        'id' => uniqid(),
                        'user_id' => \Session::get('broadcaster_id'),
                        'description' => 'Your file with file code '.$file_code. ' has been approved and pushed to the Adserver by '.\Session::get('broadcaster_id'),
                        'ip_address' => request()->ip(),
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    ];

                    $status = Utilities::switch_db('api')->table('status_logs')->insert($insertStatus);
                }
            } else {
                    $insertStatus = [
                        'id' => uniqid(),
                        'user_id' => \Session::get('broadcaster_id'),
                        'description' => 'Your file with file code '.$file_code. ' has just been rejected by '.\Session::get('broadcaster_id'),
                        'ip_address' => request()->ip(),
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    ];

                    $status = Utilities::switch_db('api')->table('status_logs')->insert($insertStatus);
            }

            //$update_file = json_decode(Api::update_fileStatus($is_file_accepted, $broadcaster_id, $file_code, $campaign_id));
            return response()->json([
                'is_file_accepted' => $update_file
            ]);

        } else {
            return;
        }
    }
}