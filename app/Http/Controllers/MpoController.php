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

        $mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpoDetails WHERE broadcaster_id = '$broadcaster_id' ORDER BY time_created DESC ");

        $mpo_data = [];

        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");

        $broadcaster_name = $broadcaster_det[0]->brand;

        foreach ($mpos as $mpo) {
            $n = 1;
            $camp = Utilities::switch_db('api')->select("SELECT * from mpos where id = '$mpo->mpo_id'");
            $campaign_id = $camp[0]->campaign_id;
            $campaign_details = Api::fetchCampaign($campaign_id);
            $brand = Api::brand($campaign_id);
            $payment_details = Api::fetchPayment($campaign_id);
            $status = Api::approvedCampaignFiles($campaign_id);
            $invoice = Utilities::switch_db('api')->select("SELECT invoice_number from invoices where campaign_id = '$campaign_id'");

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
                'id' => $mpo->agency_id ? $invoice[0]->invoice_number.'v'.$broadcaster_name[0] : $invoice[0]->invoice_number,
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $product,
                'amount' => $amount,
                'name' => $name,
                'brand' => $brand[0]->name,
                'time_created' => $time,
                'status' => $status
            ];

            $n++;
        }

        return view('mpos.index', compact('mpo_data'));
    }

    public function pending_mpos()
    {
//        fopen("/Documents/comingsoon.zip", 'r');

        $broadcaster_id = \Session::get('broadcaster_id');

        $pending_mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpoDetails WHERE is_mpo_accepted = 0 AND broadcaster_id = '$broadcaster_id' ORDER BY time_created DESC ");

        $mpo_data = [];

        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");

        $broadcaster_name = $broadcaster_det[0]->brand;

        foreach ($pending_mpos as $mpo) {
            $camp = Utilities::switch_db('api')->select("SELECT * from mpos where id = '$mpo->mpo_id'");
            $campaign_id = $camp[0]->campaign_id;
            $campaign_details = Api::fetchCampaign($campaign_id);
            $payment_details = Api::fetchPayment($campaign_id);

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
                $brand = Api::brand($campaign_id);
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

            if (Api::getOutstandingFiles($campaign_id, $broadcaster_id) === 0) {
                $files = 0;
            } else {
                $files = Api::getOutstandingFiles($campaign_id, $broadcaster_id);
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

    public function pending_mpos_data(DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');

        $pending_mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpoDetails WHERE is_mpo_accepted = 0 AND broadcaster_id = '$broadcaster_id' ORDER BY time_created DESC ");

        $mpo_data = [];
        $j = 1;

        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");

        $broadcaster_name = $broadcaster_det[0]->brand;

        foreach ($pending_mpos as $mpo) {
            $n = 1;
            $camp = Utilities::switch_db('api')->select("SELECT * from mpos where id = '$mpo->mpo_id'");
            $campaign_id = $camp[0]->campaign_id;
            if (Api::pendingMPOs($campaign_id) === true) {
                $campaign_details = Api::fetchCampaign($campaign_id);
                $payment_details = Api::fetchPayment($campaign_id);
                $invoice = Utilities::switch_db('api')->select("SELECT invoice_number from invoices where campaign_id = '$campaign_id'");


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
                    $brand = Api::brand($campaign_id);
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

                if (Api::getOutstandingFiles($campaign_id, $broadcaster_id) === 0) {
                    $files = 0;
                } else {
                    $files = Api::getOutstandingFiles($campaign_id, $broadcaster_id);
                }

                $mpo_data[] = [
                    's_n' => $j,
                    'invoice_number' => $mpo->agency_id ? $invoice[0]->invoice_number.'v'.$broadcaster_name[0] : $invoice[0]->invoice_number,
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
                $n++;
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

    public function update_file($is_file_accepted, $file_code, $rejection_reason)
    {
        if (request()->ajax()) {

            $add = Api::addFile($file_code);

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

            return response()->json([
                'is_file_accepted' => $update_file
            ]);

        } else {
            return;
        }
    }

}