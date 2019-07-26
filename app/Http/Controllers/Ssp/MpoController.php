<?php

namespace Vanguard\Http\Controllers\Ssp;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Libraries\Api;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\AdslotReason;
use Vanguard\Models\SelectedAdslot;
use Vanguard\Models\RejectionReason;
use Vanguard\Services\Company\CompanyDetails;
use Vanguard\Services\Mpo\MpoDetails;
use Vanguard\Services\Mpo\MpoList;
use Yajra\DataTables\DataTables;
use Vanguard\Services\BroadcasterPlayout\CreatePlayout;
use Vanguard\Http\Controllers\Controller;

class MpoController extends Controller
{
    use CompanyIdTrait;

    public function index()
    {
        $mpo_list_service = new MpoList($this->companyId(), null,null);
        return view('broadcaster_module.mpos.index')
            ->with('companies_id', \Auth::user()->companies()->count() > 1 ? $mpo_list_service->getMpoCompanyId() : '')
            ->with('status', '')
            ->with('pageLabel', "All MPOs");;
    }

    public function getAllData(Request $request, DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpo_list_service = new MpoList($this->companyId(), $request->start_date, $request->stop_date, $request->status);
        $mpos = $mpo_list_service->mpoList();
        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);
        return $this->mpoDatatablesCollection($dataTables, $mpo_data);
    }

    public function filterCompany(DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpo_list_service = new MpoList(request()->channel_id, request()->start_date, request()->stop_date);
        $mpos = $mpo_list_service->mpoList();
        $mpo_data = $this->getMpoCollection($mpos, $broadcaster_id);
        return $this->mpoDatatablesCollection($dataTables, $mpo_data);
    }

    public function pending_mpos()
    {
        // return view('broadcaster_module.mpos.pending_mpo');
        $mpo_list_service = new MpoList($this->companyId(), null, null);
        return view('broadcaster_module.mpos.index')
            ->with('companies_id', \Auth::user()->companies()->count() > 1 ? $mpo_list_service->getMpoCompanyId() : '')
            ->with('status', 'pending')
            ->with('pageLabel', "Pending MPOs");

    }

    public function pendingData(Request $request, DataTables $dataTables)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpo_list_service = new MpoList($this->companyId(), $request->start_date, $request->stop_date);
        $mpo_data = $this->getMpoCollection($mpo_list_service->pendingMpoList(), $broadcaster_id);
        return $this->mpoDatatablesCollection($dataTables, $mpo_data);
    }

    public function mpoAction($mpo_id)
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $mpo_details_service = new MpoDetails($mpo_id, $this->companyId());
        $mpo_data = $this->getMpoCollection($mpo_details_service->getMpoDetails(), $broadcaster_id);
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
                    $update = Utilities::switch_db('api')->update("UPDATE mpoDetails set is_mpo_accepted = 1 where 
                                                                    mpo_id = '$mpo_id' and broadcaster_id = '$broadcaster_id'");
                    //change the status of the campaign if campaign date is the current date
                    $today_date = date("Y-m-d");
                    $update_campaign_status = Utilities::switch_db('api')->update("UPDATE campaignDetails 
                                                                                        SET status = 'active' 
                                                                                        WHERE start_date = '$today_date' 
                                                                                        AND campaign_id = '$campaign_id' 
                                                                                        AND broadcaster = '$broadcaster_id'");

                    //The mpo has been approved, so let us create the playout
                    $playout_creator = new CreatePlayout($campaign_id, $mpo_id, $this->companyId());
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
        foreach ($mpos as $mpo) {
            $status = Api::approvedCampaignFiles($mpo->campaign_id, $broadcaster_id);
            $outstanding_files = Api::getOutstandingFiles($mpo->campaign_id, $broadcaster_id);

            if (count($outstanding_files) === 0) {
                $files = 0;
            } else {
                $files = $outstanding_files;
            }

            $mpo_data[] = [
                'mpo_id' => $mpo->mpo_id,
                'id' => $mpo->agency_id ? $mpo->invoice_number.'v'.$this->getCompanyName($broadcaster_id)->name : $mpo->invoice_number,
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $mpo->product,
                'budget' => $mpo->amount,
                'name' => $mpo->name,
                'brand' => $mpo->brand_name,
                'date_created' => date('Y-m-d', strtotime($mpo->date)),
                'status' => $status['mpo_approval_status'],
                'channel' => $this->getCompanyName($broadcaster_id)->name,
                'files' => $files,
                'campaign_status' => $mpo->campaign_status,
                'station' => \Auth::user()->companies()->count() > 1 ? $this->getCompanyName($mpo->company_id)->name : ''
            ];

        }

        return $mpo_data;
    }

    public function getCompanyName($company_id)
    {
        $company_details_service = new CompanyDetails($company_id);
        return $company_details_service->getCompanyDetails();
    }

    public static function mpoDatatablesCollection($dataTables, $mpo_data)
    {
        return $dataTables->collection($mpo_data)
            ->editColumn('status', function ($mpo_data){
                if($mpo_data['is_mpo_accepted'] == 1){
                    return '<span class="span_state status_success">All File Approved</span>';
                }elseif($mpo_data['campaign_status'] == 'pending' || $mpo_data['campaign_status'] == 'file_error') {
                    if(\Auth::user()->companies()->count() > 1){
                        return '<span class="span_state status_danger modal_mpo_click">In Progress</span>';
                    }else{
                        if(\Auth::user()->hasPermissionTo('view.mpo')){
                            return '<a href="'.route('mpo.action', ['mpo_id' => $mpo_data['mpo_id']]).'" class="span_state status_danger modal_mpo_click">In Review</a>';
                        }
                    }
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
