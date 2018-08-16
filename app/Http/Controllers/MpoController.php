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
        $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d, mpos as m where m_d.broadcaster_id = '$broadcaster_id' and m.id = m_d.mpo_id order by m_d.time_created desc");
        $mpo_data = [];
        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
        $broadcaster_name = $broadcaster_det[0]->brand;

        foreach ($mpos as $mpo) {
            $n = 1;
            $campaign = Utilities::switch_db('api')->select("SELECT c.name, c.product, c.time_created, b.name as brand_name, i.invoice_number from campaignDetails as c, brands as b, invoices as i where c.campaign_id = '$mpo->campaign_id' and c.brand = b.id and i.campaign_id = c.campaign_id");
            $payment_details = Api::fetchPayment($mpo->campaign_id, $broadcaster_id);
            $status = Api::approvedCampaignFiles($mpo->campaign_id, $broadcaster_id);

            if (count($campaign) === 0) {
                $product = 0;
                $name = 0;
                $time = 0;
            } else {
                $product = $campaign[0]->product;
                $name = $campaign[0]->name;
                $time = date('Y-m-d', strtotime($campaign[0]->time_created));
            }


            if (count($payment_details) === 0) {
                $amount = 0;
            } else {
                $amount = $payment_details[0]->amount;
            }

            if (Api::getOutstandingFiles($mpo->campaign_id, $broadcaster_id) === 0) {
                $files = 0;
            } else {
                $files = Api::getOutstandingFiles($mpo->campaign_id, $broadcaster_id);
            }

            $mpo_data[] = [
                'mpo_id' => $mpo->mpo_id,
                'id' => $mpo->agency_id ? $campaign[0]->invoice_number.'v'.$broadcaster_name[0] : $campaign[0]->invoice_number,
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $product,
                'amount' => $amount,
                'name' => $name,
                'brand' => $campaign[0]->brand_name,
                'time_created' => $time,
                'status' => $status,
                'channel' => $broadcaster_name,
                'files' => $files
            ];

        }

        return view('broadcaster_module.mpos.index', compact('mpo_data'));
    }

    public function getAllData(Request $request, DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        if($request->has('start_date') && $request->has('stop_date')) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d, mpos as m where m_d.broadcaster_id = '$broadcaster_id' and m.id = m_d.mpo_id and m_d.time_created between '$start_date' and '$stop_date' order by m_d.time_created desc");
        }else{
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d, mpos as m where m_d.broadcaster_id = '$broadcaster_id' and m.id = m_d.mpo_id order by m_d.time_created desc");
        }
        $mpo_data = [];
        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
        $broadcaster_name = $broadcaster_det[0]->brand;

        foreach ($mpos as $mpo) {
            $n = 1;
            $campaign = Utilities::switch_db('api')->select("SELECT c.name, c.product, c.time_created, b.name as brand_name, i.invoice_number from campaignDetails as c, brands as b, invoices as i where c.campaign_id = '$mpo->campaign_id' and c.brand = b.id and i.campaign_id = c.campaign_id");
            $payment_details = Api::fetchPayment($mpo->campaign_id, $broadcaster_id);
            $status = Api::approvedCampaignFiles($mpo->campaign_id, $broadcaster_id);

            if (count($campaign) === 0) {
                $product = 0;
                $name = 0;
                $time = 0;
            } else {
                $product = $campaign[0]->product;
                $name = $campaign[0]->name;
                $time = date('Y-m-d', strtotime($campaign[0]->time_created));
            }

            if (count($payment_details) === 0) {
                $amount = 0;
            } else {
                $amount = $payment_details[0]->amount;
            }

            $mpo_data[] = [
                'mpo_id' => $mpo->mpo_id,
                'id' => $mpo->agency_id ? $campaign[0]->invoice_number.'v'.$broadcaster_name[0] : $campaign[0]->invoice_number,
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $product,
                'budget' => $amount,
                'name' => $name,
                'brand' => $campaign[0]->brand_name,
                'date_created' => $time,
                'status' => $status
            ];

            $n++;
        }

        return $dataTables->collection($mpo_data)
            ->editColumn('status', function ($mpo_data){
                if($mpo_data['status'] === true){
                    return '<span class="span_state status_success">Approved</span>';
                }else {
                    return '<a href="'.route('mpo.action', ['mpo_id' => $mpo_data['mpo_id']]).'" class="span_state status_danger modal_mpo_click">Pending</a>';
                }
            })
            ->rawColumns(['status' => 'status', 'name' => 'name'])
            ->addIndexColumn()
            ->make(true);
    }

    public function pending_mpos()
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d, mpos as m where m_d.broadcaster_id = '$broadcaster_id' and m.id = m_d.mpo_id order by m_d.time_created desc");
        $mpo_data = [];
        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
        $broadcaster_name = $broadcaster_det[0]->brand;

        foreach ($mpos as $mpo) {
            $n = 1;
            $campaign = Utilities::switch_db('api')->select("SELECT c.name, c.product, c.time_created, b.name as brand_name, i.invoice_number from campaignDetails as c, brands as b, invoices as i where c.campaign_id = '$mpo->campaign_id' and c.brand = b.id and i.campaign_id = c.campaign_id");
            $payment_details = Api::fetchPayment($mpo->campaign_id, $broadcaster_id);
            $status = Api::pendingMPOs($mpo->campaign_id, $broadcaster_id);

            if (count($campaign) === 0) {
                $product = 0;
                $name = 0;
                $time = 0;
            } else {
                $product = $campaign[0]->product;
                $name = $campaign[0]->name;
                $time = date('Y-m-d', strtotime($campaign[0]->time_created));
            }

            if (count($payment_details) === 0) {
                $amount = 0;
            } else {
                $amount = $payment_details[0]->amount;
            }

            if (Api::getOutstandingFiles($mpo->campaign_id, $broadcaster_id) === 0) {
                $files = 0;
            } else {
                $files = Api::getOutstandingFiles($mpo->campaign_id, $broadcaster_id);
            }

            $mpo_data[] = [
                'mpo_id' => $mpo->mpo_id,
                'id' => $mpo->agency_id ? $campaign[0]->invoice_number.'v'.$broadcaster_name[0] : $campaign[0]->invoice_number,
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $product,
                'amount' => $amount,
                'name' => $name,
                'brand' => $campaign[0]->brand_name,
                'time_created' => $time,
                'status' => $status,
                'channel' => $broadcaster_name,
                'files' => $files
            ];


        }

        return view('broadcaster_module.mpos.pending_mpo', compact('mpo_data'));
    }

//    public function pending_mpos_data(DataTables $dataTables)
//    {
//        $broadcaster_id = \Session::get('broadcaster_id');
//
//        $pending_mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpoDetails WHERE is_mpo_accepted = 0 AND broadcaster_id = '$broadcaster_id' ORDER BY time_created DESC ");
//
//        $mpo_data = [];
//        $j = 1;
//
//        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
//
//        $broadcaster_name = $broadcaster_det[0]->brand;
//
//        foreach ($pending_mpos as $mpo) {
//            $n = 1;
//            $camp = Utilities::switch_db('api')->select("SELECT * from mpos where id = '$mpo->mpo_id'");
//            $campaign_id = $camp[0]->campaign_id;
//            if (Api::pendingMPOs($campaign_id) === true) {
//                $campaign_details = Api::fetchCampaign($campaign_id);
//                $payment_details = Api::fetchPayment($campaign_id, $broadcaster_id);
//                $invoice = Utilities::switch_db('api')->select("SELECT invoice_number from invoices where campaign_id = '$campaign_id'");
//
//
//                if (count($campaign_details) === 0) {
//                    $product = 0;
//                    $brand = 0;
//                    $name = 0;
//                    $time = 0;
//                    $start_date = 0;
//                    $start_end = 0;
//                    $channel = 0;
//                } else {
//                    $product = $campaign_details[0]->product;
//                    $brand = Api::brand($campaign_id);
//                    $name = $campaign_details[0]->name;
//                    $time = date('Y-m-d', strtotime($campaign_details[0]->time_created));
//                    $start_date = $campaign_details[0]->start_date;
//                    $start_end = $campaign_details[0]->stop_date;
//                    $channel = Api::getChannelName($campaign_details[0]->channel)[0]->channel;
//                }
//
//                if (count($payment_details) === 0) {
//                    $amount = 0;
//                } else {
//                    $amount = $payment_details[0]->amount;
//                }
//
//                if (Api::getOutstandingFiles($campaign_id, $broadcaster_id) === 0) {
//                    $files = 0;
//                } else {
//                    $files = Api::getOutstandingFiles($campaign_id, $broadcaster_id);
//                }
//
//                $mpo_data[] = [
//                    's_n' => $j,
//                    'invoice_number' => $mpo->agency_id ? $invoice[0]->invoice_number.'v'.$broadcaster_name[0] : $invoice[0]->invoice_number,
//                    'id' => $mpo->id,
//                    'is_mpo_accepted' => $mpo->is_mpo_accepted,
//                    'product' => $product,
//                    'brand' => $brand[0]->name,
//                    'campaign_name' => $name,
//                    'channel' => $channel,
//                    'time_created' => $time,
//                    'start_date' => date('M j, Y h:ia', strtotime($start_date)),
//                    'stop_date' => date('M j, Y h:ia', strtotime($start_end)),
//                    'files' => $files,
//                    'amount' => $amount
//                ];
//                $j++;
//                $n++;
//            }
//
//
//        }
//
//        return $dataTables->collection($mpo_data)
//            ->addColumn('view', function ($mpo_data) {
//                return '
//                        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal' . $mpo_data['id'] . '" style="font-size: 16px">
//                            View
//                        </button>
//                    ';
//            })
//            ->rawColumns(['view' => 'view'])->addIndexColumn()
//            ->make(true);
//    }

    public function mpoAction($mpo_id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d, mpos as m where m_d.broadcaster_id = '$broadcaster_id' and m.id = m_d.mpo_id and m_d.mpo_id = '$mpo_id' ");
        $mpo_data = [];
        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
        $broadcaster_name = $broadcaster_det[0]->brand;
        $campaign_id = $mpos[0]->campaign_id;
        $campaign = Utilities::switch_db('api')->select("SELECT c.name, c.product, c.time_created, b.name as brand_name, i.invoice_number from campaignDetails as c, brands as b, invoices as i where c.campaign_id = '$campaign_id' and c.brand = b.id and i.campaign_id = c.campaign_id");
        $payment_details = Api::fetchPayment($campaign_id, $broadcaster_id);
        $status = Api::approvedCampaignFiles($campaign_id, $broadcaster_id);

        if (count($campaign) === 0) {
            $product = 0;
            $name = 0;
            $time = 0;
        } else {
            $product = $campaign[0]->product;
            $name = $campaign[0]->name;
            $time = date('Y-m-d', strtotime($campaign[0]->time_created));
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

        $mpo_data = [
            'mpo_id' => $mpo_id,
            'id' => $mpos[0]->agency_id ? $campaign[0]->invoice_number.'v'.$broadcaster_name[0] : $campaign[0]->invoice_number,
            'is_mpo_accepted' => $mpos[0]->is_mpo_accepted,
            'product' => $product,
            'amount' => $amount,
            'name' => $name,
            'brand' => $campaign[0]->brand_name,
            'time_created' => $time,
            'status' => $status,
            'channel' => $broadcaster_name,
            'files' => $files
        ];

        return view('broadcaster_module.mpos.action', compact('mpo_data'));
    }

    public function update_file($is_file_accepted, $file_code, $rejection_reason)
    {
        if (request()->ajax()) {

//            $add = Api::addFile($file_code);

            if ($is_file_accepted !== 'null' && $rejection_reason === 'null') {
                $update_file = Utilities::switch_db('reports')->update("UPDATE files SET is_file_accepted = '$is_file_accepted' WHERE file_code = '$file_code'");
            }else if ($is_file_accepted === 'null' && $rejection_reason !== 'null') {
                $update_file = Utilities::switch_db('reports')->update("UPDATE files SET rejection_reason = '$rejection_reason' WHERE file_code = '$file_code'");
            }else if ($is_file_accepted !== 'null' && $rejection_reason !== 'null') {
                $update_file = Utilities::switch_db('reports')->update("UPDATE files SET is_file_accepted = '$is_file_accepted', rejection_reason = '$rejection_reason' WHERE file_code = '$file_code'");
            }

            //api call
            if($is_file_accepted === "1"){

//                if ( $file_code) {
                    $insertStatus = [
                        'id' => uniqid(),
                        'user_id' => \Session::get('broadcaster_id'),
                        'description' => 'Your file with file code '.$file_code. ' has been approved and pushed to the Adserver by '.\Session::get('broadcaster_id'),
                        'ip_address' => request()->ip(),
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    ];

                    $status = Utilities::switch_db('api')->table('status_logs')->insert($insertStatus);
//                }
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