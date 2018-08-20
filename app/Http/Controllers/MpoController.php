<?php

namespace Vanguard\Http\Controllers;

use Symfony\Component\VarDumper\Cloner\Data;
use Vanguard\Libraries\Api;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;

class MpoController extends Controller
{
    
    public function index(Request $request)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        if($request->has('start_date') && $request->has('stop_date')) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d JOIN mpos as m ON m.id = m_d.mpo_id where m_d.broadcaster_id = '$broadcaster_id' and m_d.time_created between '$start_date' and '$stop_date' order by m_d.time_created desc");
        }else{
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d JOIN mpos as m ON m.id = m_d.mpo_id where m_d.broadcaster_id = '$broadcaster_id' order by m_d.time_created desc");
        }
        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);

        return view('broadcaster_module.mpos.index', compact('mpo_data'));
    }

    public function getAllData(Request $request, DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        if($request->has('start_date') && $request->has('stop_date')) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d JOIN mpos as m ON m.id = m_d.mpo_id where m_d.broadcaster_id = '$broadcaster_id' and m_d.time_created between '$start_date' and '$stop_date' order by m_d.time_created desc");
        }else{
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d JOIN mpos as m ON m.id = m_d.mpo_id where m_d.broadcaster_id = '$broadcaster_id' order by m_d.time_created desc");
        }
        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);

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

    public function pending_mpos(Request $request)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        if($request->has('start_date') && $request->has('stop_date')) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d JOIN mpos as m ON m.id = m_d.mpo_id where m_d.broadcaster_id = '$broadcaster_id' and m_d.is_mpo_accepted = 0 and m_d.time_created between '$start_date' and '$stop_date' order by m_d.time_created desc");
        }else{
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d JOIN mpos as m ON m.id = m_d.mpo_id where m_d.broadcaster_id = '$broadcaster_id' and m_d.is_mpo_accepted = 0 order by m_d.time_created desc");
        }

        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);

        return view('broadcaster_module.mpos.pending_mpo', compact('mpo_data'));
    }

    public function pendingData(Request $request, DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        if($request->has('start_date') && $request->has('stop_date')) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d, mpos as m where m_d.broadcaster_id = '$broadcaster_id' and m_d.is_mpo_accepted = 0 and m.id = m_d.mpo_id and m_d.time_created between '$start_date' and '$stop_date' order by m_d.time_created desc");
        }else{
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d, mpos as m where m_d.broadcaster_id = '$broadcaster_id' and m_d.is_mpo_accepted = 0 and m.id = m_d.mpo_id order by m_d.time_created desc");
        }
        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);

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

    public function mpoAction($mpo_id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d JOIN mpos as m ON m.id = m_d.mpo_id where m_d.broadcaster_id = '$broadcaster_id' and m_d.mpo_id = '$mpo_id' ");
        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);

        return view('broadcaster_module.mpos.action', compact('mpo_data'));
    }

    public function update_file($is_file_accepted, $file_code, $rejection_reason, $campaign_id)
    {
        if (request()->ajax()) {

            $broadcaster_id = \Session::get('broadcaster_id');
//            $add = Api::addFile($file_code);
            $check_files = Api::checkFilesForUpdatingMpos($campaign_id, $broadcaster_id);

            if($check_files == 1){
                $update_mpo_details = Utilities::switch_db('api')->update("UPDATE mpoDetails set is_mpo_accepted = 1 where mpo_id = (SELECT id from mpos where campaign_id = '$campaign_id') and broadcaster_id = '$broadcaster_id'");
            }

            if ($is_file_accepted !== 'null' && $rejection_reason === 'null') {
                $update_file = Utilities::switch_db('reports')->update("UPDATE files SET is_file_accepted = '$is_file_accepted' WHERE file_code = '$file_code'");
            }else if ($is_file_accepted === 'null' && $rejection_reason !== 'null') {
                $update_file = Utilities::switch_db('reports')->update("UPDATE files SET rejection_reason = '$rejection_reason' WHERE file_code = '$file_code'");
            }else if ($is_file_accepted !== 'null' && $rejection_reason !== 'null') {
                $update_file = Utilities::switch_db('reports')->update("UPDATE files SET is_file_accepted = '$is_file_accepted', rejection_reason = '$rejection_reason' WHERE file_code = '$file_code'");
            }

            //api call

            $insertStatus = [
                'id' => uniqid(),
                'user_id' => \Session::get('broadcaster_id'),
                'description' => $is_file_accepted == "1" ? 'Your file with file code '.$file_code. ' has been approved and pushed to the Adserver by '.\Session::get('broadcaster_id') : 'Your file with file code '.$file_code. ' has just been rejected by '.\Session::get('broadcaster_id'),
                'ip_address' => request()->ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            ];

            $status = Utilities::switch_db('api')->table('status_logs')->insert($insertStatus);

            return response()->json([
                'is_file_accepted' => $update_file
            ]);

        } else {
            return;
        }

    }

    public function getMpoCollection($mpos, $broadcaster_id)
    {

        $mpo_data = [];
        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
        $broadcaster_name = $broadcaster_det[0]->brand;

        foreach ($mpos as $mpo) {
            $n = 1;
            $campaign = Utilities::switch_db('api')->select("SELECT c.name, c.product, c.time_created, b.name as brand_name, i.invoice_number from campaignDetails as c JOIN brands as b ON c.brand = b.id JOIN invoices as i ON i.campaign_id = c.campaign_id where c.campaign_id = '$mpo->campaign_id'");
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

            $outstanding_files = Api::getOutstandingFiles($mpo->campaign_id, $broadcaster_id);
            
            if ($outstanding_files === 0) {
                $files = 0;
            } else {
                $files = $outstanding_files;
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
                'status' => $status,
                'channel' => $broadcaster_name,
                'files' => $files
            ];

        }

        return $mpo_data;
    }

}