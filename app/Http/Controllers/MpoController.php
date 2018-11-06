<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Libraries\AmazonS3;
use Vanguard\Libraries\Api;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\AdslotReason;
use Vanguard\Models\SelectedAdslot;
use Vanguard\Models\RejectionReason;
use Yajra\DataTables\DataTables;
use Vanguard\Services\BroadcasterPlayout\CreatePlayout;

class MpoController extends Controller
{

    public function index()
    {
        return view('broadcaster_module.mpos.index');
    }

    public function getAllData(Request $request, DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        if($request->has('start_date') && $request->has('stop_date')) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, c_d.status as campaign_status, m_d.agency_id, m.campaign_id from mpoDetails as m_d
                                                            INNER JOIN mpos as m ON m.id = m_d.mpo_id
                                                            INNER JOIN campaignDetails as c_d ON c_d.campaign_id = m.campaign_id AND c_d.broadcaster = m_d.broadcaster_id
                                                            where m_d.broadcaster_id = '$broadcaster_id' and c_d.status != 'on_hold' OR c_d.status = 'file_error' AND
                                                            and m_d.time_created between '$start_date' and '$stop_date' AND c_d.broadcaster = '$broadcaster_id' order by m_d.time_created desc");
        }else{
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, c_d.status as campaign_status, m_d.agency_id, m.campaign_id from mpoDetails as m_d
                                                            INNER JOIN mpos as m ON m.id = m_d.mpo_id
                                                            INNER JOIN campaignDetails as c_d ON c_d.campaign_id = m.campaign_id AND c_d.broadcaster = m_d.broadcaster_id
                                                            where m_d.broadcaster_id = '$broadcaster_id' and c_d.status != 'on_hold' OR c_d.status = 'file_error'
                                                            AND c_d.broadcaster = '$broadcaster_id'
                                                            order by m_d.time_created desc");
        }

        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);

        return $this->mpoDatatablesCollection($dataTables, $mpo_data);
    }

    public function pending_mpos()
    {
        return view('broadcaster_module.mpos.pending_mpo');
    }

    public function pendingData(Request $request, DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        if($request->has('start_date') && $request->has('stop_date')) {
            $start_date = $request->start_date;
            $stop_date = $request->stop_date;
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, c_d.status as campaign_status m_d.agency_id, m.campaign_id from mpoDetails as m_d
                                                            INNER JOIN mpos as m ON m.id = m_d.mpo_id
                                                            INNER JOIN campaignDetails as c_d ON c_d.campaign_id = m.campaign_id AND c_d.broadcaster = m_d.broadcaster_id
                                                            where m_d.broadcaster_id = '$broadcaster_id' and c_d.status != 'on_hold' AND
                                                            c_d.broadcaster = '$broadcaster_id' AND
                                                            m_d.is_mpo_accepted = 0 and m_d.time_created between '$start_date' and '$stop_date' order by m_d.time_created desc");
        }else{
            $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, c_d.status as campaign_status, m_d.agency_id, m.campaign_id from mpoDetails as m_d
                                                            INNER JOIN mpos as m ON m.id = m_d.mpo_id
                                                            INNER JOIN campaignDetails as c_d ON c_d.campaign_id = m.campaign_id AND c_d.broadcaster = m_d.broadcaster_id
                                                            where m_d.broadcaster_id = '$broadcaster_id' and c_d.status != 'on_hold' AND
                                                            c_d.broadcaster = '$broadcaster_id' AND
                                                            m_d.is_mpo_accepted = 0 order by m_d.time_created desc");
        }

        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);

        return $this->mpoDatatablesCollection($dataTables, $mpo_data);
    }

    public function mpoAction($mpo_id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpos = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, c_d.status as campaign_status, m_d.agency_id, m.campaign_id from mpoDetails as m_d
                                                         INNER JOIN mpos as m ON m.id = m_d.mpo_id and m_d.mpo_id = '$mpo_id'
                                                         INNER JOIN campaignDetails as c_d ON c_d.campaign_id = m.campaign_id AND c_d.broadcaster = m_d.broadcaster_id
                                                         where m_d.broadcaster_id = '$broadcaster_id' AND c_d.broadcaster = '$broadcaster_id'
                                                         and c_d.status = 'pending' OR c_d.status = 'file_error'");
        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);
        $reject_reasons = RejectionReason::all();
        if(count($mpo_data) == 0){
            \Session::flash('success', 'All files approved and the cmpaign is now active');
            return redirect()->route('all-mpos');
        }
        $count_mpo_data_files = count((array)$mpo_data[0]['files'][0]);
        $mpo_data_files = $mpo_data[0]['files'];

        //add pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($mpo_data_files);
        $perPage = 5;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $mpo_data_files = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $mpo_data_files->setPath($mpo_id);
        return view('broadcaster_module.mpos.action', compact('mpo_data', 'reject_reasons', 'mpo_data_files', 'count_mpo_data_files'));
    }

    public function update_file($file_code, $campaign_id, $mpo_id)
    {

        $status = \request()->status;
        $broadcaster_id = \Session::get('broadcaster_id');
        $rejection_reasons = \request()->rejection_reason;
        $recommendation = \request()->recommendation;
        if($status === 'rejected' && $rejection_reasons === null){
            return response()->json([
                'error' => 'error'
            ]);
        }

//            $add = Api::addFile($file_code);
        try {
            \DB::transaction(function () use ($file_code, $status, $campaign_id, $broadcaster_id, $mpo_id, $rejection_reasons, $recommendation) {
                $file = SelectedAdslot::where('file_code', $file_code)->first();

                if($status == 'rejected'){
                    foreach ($rejection_reasons as $rejection_reason){
                        AdslotReason::create([
                           'selected_adslot_id' => $file->id,
                           'rejection_reason_id' => (int)$rejection_reason,
                            'user_id' => $broadcaster_id,
                            'recommendation' => $recommendation
                        ]);
                    }
                }
                $file->status = $status;
                $file->save();
                $check_for_rejected_files = SelectedAdslot::where([['campaign_id', $campaign_id],['status', 'rejected']])->first();
                if($check_for_rejected_files){
                    Utilities::switch_db('api')->update("UPDATE campaignDetails set status = 'file_errors' WHERE campaign_id = '$campaign_id'");
                }
                $check_files = Api::approvedCampaignFiles($campaign_id, $broadcaster_id);
                if($check_files['check_file_for_updating_mpo'] == 0){
                    Utilities::switch_db('api')->update("UPDATE mpoDetails set is_mpo_accepted = 1 where mpo_id = '$mpo_id' and broadcaster_id = '$broadcaster_id'");

                    //change the status of the campaign if campaign date is the current date
                    $today_date = date("Y-m-d");
                    $update_campaign_status = Utilities::switch_db('api')->update("UPDATE campaignDetails 
                                                                                        SET status = 'active' 
                                                                                        WHERE start_date = '$today_date' 
                                                                                        AND campaign_id = '$campaign_id' 
                                                                                        AND broadcaster = '$broadcaster_id'");

                    //The mpo has been approved, so let us create the playout
                    $playout_creator = new CreatePlayout($campaign_id, $mpo_id);
                    $playout_creator->run();
                }
                $description = $status == "approved" ? 'Your file with file code '.$file_code. ' has been approved ' : 'Your file with file code '.$file_code. ' has been rejected';
                Api::saveActivity(\Session::get('broadcaster_id'), $description);
            });
        }catch (\Exception $e){
            return response()->json([
                'error' => 'error'
            ]);
        }

        return response()->json([
            'status' => 'approved'
        ]);

    }

    public function getMpoCollection($mpos, $broadcaster_id)
    {
        $mpo_data = [];
        $broadcaster_det = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
        $broadcaster_name = $broadcaster_det[0]->brand;

        foreach ($mpos as $mpo) {

            $campaign = Utilities::switch_db('api')->select("SELECT c.name, c.product, c.time_created, b.name as brand_name, i.invoice_number from campaignDetails as c
                                                                  INNER JOIN brands as b ON c.brand = b.id JOIN invoices as i ON i.campaign_id = c.campaign_id
                                                                  where c.campaign_id = '$mpo->campaign_id'");
            $payment_details = Api::fetchPayment($mpo->campaign_id, $broadcaster_id);
            $status = Api::approvedCampaignFiles($mpo->campaign_id, $broadcaster_id);
            $outstanding_files = Api::getOutstandingFiles($mpo->campaign_id, $broadcaster_id);

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

            if (count($outstanding_files) === 0) {
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
                'status' => $status['mpo_approval_status'],
                'channel' => $broadcaster_name,
                'files' => $files,
                'campaign_status' => $mpo->campaign_status
            ];

        }

        return $mpo_data;
    }

    public static function mpoDatatablesCollection($dataTables, $mpo_data)
    {
        return $dataTables->collection($mpo_data)
            ->editColumn('status', function ($mpo_data){
                if($mpo_data['is_mpo_accepted'] == 1){
                    return '<span class="span_state status_success">All File Approved</span>';
                }elseif($mpo_data['campaign_status'] == 'pending' || $mpo_data['campaign_status'] == 'file_error') {
                    return '<a href="'.route('mpo.action', ['mpo_id' => $mpo_data['mpo_id']]).'" class="span_state status_danger modal_mpo_click">In Progress</a>';
                }else{
                    return '<span class="span_state status_pending">Expired</span>';
                }
            })
            ->rawColumns(['status' => 'status', 'name' => 'name'])
            ->addIndexColumn()
            ->make(true);
    }

    public function rejectedFiles($mpo_id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpo = Utilities::switch_db('api')->select("SELECT m_d.mpo_id, m_d.is_mpo_accepted, m_d.agency_id, m.campaign_id from mpoDetails as m_d
                                                            INNER JOIN mpos as m ON m.id = m_d.mpo_id where m_d.mpo_id = '$mpo_id' and
                                                            m_d.broadcaster_id = '$broadcaster_id' AND m_d.is_mpo_accepted = 0 order by m_d.time_created desc");
        $mpo_data = $this->getMpoCollection($mpo, $broadcaster_id);
        return view('mpos.rejected_files', compact('mpo_data'));
    }

    public function updateFiles(Request $request, $file_id)
    {
        $file = SelectedAdslot::where('id', $file_id)->first();

        try {
            \DB::transaction(function () use ($file,$request) {
                $file->file_name = $request->file_name;
                $file->file_url = $request->file_url;
                $file->format = $request->file_format;
                $file->status = 'pending';
                $file->save();

                $campaign_file_error = SelectedAdslot::where([['campaign_id', $file->campaign_id],['status', 'file_errors']])->first();
                if(!$campaign_file_error){
                    Utilities::switch_db('api')->update("UPDATE campaignDetails set status = 'pending' WHERE campaign_id = '$file->campaign_id'");
                }


            });
        }catch (\Exception $e){
            return response()->json(['error' => 'error']);
        }

        return response()->json(['success' => 'success']);

    }

}