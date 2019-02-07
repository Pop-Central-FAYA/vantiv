<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Services\Company\CompanyDetails;
use Yajra\DataTables\DataTables;

class AllCampaign
{
    protected $broadcaster_id;
    protected $agency_id;
    protected $request;
    protected $dashboard;
    protected $company_ids;

    public function __construct($request, $broadcaster_id, $agency_id, $dashboard, $company_ids)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->request = $request;
        $this->dashboard = $dashboard;
        $this->company_ids = $company_ids;
        if($company_ids && \Auth::user()->companies()->count() > 1){
            $this->broadcaster_id = null;
        }
    }

    public function run()
    {
        return $this->campaignsDataToDatatables();
    }

    public function campaignsDataToDatatables()
    {
        $datatables = new DataTables();

        $campaigns = $this->fetchAllCampaigns();

        $campaigns = $this->getCampaignDatatables($campaigns);

        return $datatables->collection($campaigns)
            ->addColumn('name', function ($campaigns) {
                if($this->broadcaster_id){
                    if($campaigns['status'] === 'on_hold'){
                        return '<a href="'.route('broadcaster.campaign.hold').'">'.$campaigns['name'].'</a>';
                    }else{
                        return '<a href="'.route('broadcaster.campaign.details', ['id' => $campaigns['campaign_id']]).'">'.$campaigns['name'].'</a>';
                    }
                }else{
                    if($campaigns['status'] === 'on_hold'){
                        return '<a href="'.route('agency.campaigns.hold').'">'.$campaigns['name'].'</a>';
                    }else{
                        return '<a href="'.route('agency.campaign.details', ['id' => $campaigns['campaign_id']]).'">'.$campaigns['name'].'</a>';
                    }
                }
            })
            ->editColumn('status', function ($campaigns){
                if($campaigns['status'] === "on_hold"){
                    return '<span class="span_state status_on_hold">On Hold</span>';
                }elseif ($campaigns['status'] === "pending"){
                    return '<span class="span_state status_pending">Pending</span>';
                }elseif ($campaigns['status'] === 'expired'){
                    return '<span class="span_state status_danger">Finished</span>';
                }elseif($campaigns['status'] === 'active') {
                    return '<span class="span_state status_success">Active</span>';
                }else {
                    return '<span class="span_state status_danger">File Errors</span>';
                }
            })
            ->rawColumns(['status' => 'status', 'name' => 'name'])
            ->addIndexColumn()
            ->make(true);
    }

    public function fetchAllCampaigns()
    {
        if($this->request->filter_user){
            return $this->filterCampaigns();
        }else{
            return $this->allCampaigns();
        }
    }

    public function baseQuery()
    {
        return \DB::table('campaignDetails')
            ->join('campaigns', 'campaigns.id', '=', 'campaignDetails.campaign_id')
            ->join('payments', 'payments.campaign_id', '=', 'campaignDetails.campaign_id')
            ->join('brands', 'brands.id', '=', 'campaignDetails.brand')
            ->select(   'campaignDetails.adslots_id',
                'campaignDetails.stop_date',
                'campaignDetails.start_date',
                'campaignDetails.status',
                'campaignDetails.time_created',
                'campaignDetails.product',
                'campaignDetails.name',
                'campaignDetails.campaign_id',
                'campaignDetails.launched_on',
                'payments.total',
                'brands.name AS brand_name',
                'campaigns.campaign_reference'
            )
            ->when($this->request->start_date && $this->request->stop_date, function ($query) {
                return $query->between('campaignDetails.start_date', $this->request->start_date,
                    $this->request->stop_date);
            });
    }

    public function allCampaigns()
    {
        return $this->baseQuery()
                                ->when(!$this->dashboard, function($query){
                                    return $query->where('campaignDetails.status', CampaignStatus::ACTIVE_CAMPAIGN);
                                })
                                ->when($this->company_ids, function($query) {
                                    return $query->whereIn('launched_on', $this->company_ids);
                                })
                                ->when($this->broadcaster_id, function($query) {
                                    return $query->where([
                                        ['campaignDetails.broadcaster', $this->broadcaster_id],
                                        ['campaignDetails.adslots', '>', 0]
                                    ]);
                                })
                                ->when($this->agency_id, function ($query) {
                                    return $query->where([
                                            ['campaignDetails.agency', $this->agency_id],
                                            ['campaignDetails.adslots', '>', 0]
                                        ])
                                        ->groupBy('campaignDetails.campaign_id');

                                })
                                ->orderBy('campaignDetails.time_created', 'DESC')
                                ->get();
    }

    public function filterCampaigns()
    {
        return $this->baseQuery()
                                ->when(($this->request->filter_user == 'agency'), function ($query) {
                                    return $query->where('campaignDetails.agency_broadcaster', $this->broadcaster_id);
                                })
                                ->when(($this->request->filter_user == 'broadcaster'), function($query) {
                                    $query->when($this->company_ids, function($inner_query) {
                                                return $inner_query->whereIn('launched_on', $this->company_ids);
                                            })
                                        ->when($this->broadcaster_id, function ($inner_query) {
                                            return $inner_query->where([
                                                ['campaignDetails.broadcaster', $this->broadcaster_id],
                                                ['campaignDetails.agency', '']
                                            ]);
                                        });
                                })
                                ->when(\Request::is('campaign/active-campaigns'), function($query){
                                    return $query->where('campaignDetails.status', CampaignStatus::ACTIVE_CAMPAIGN);
                                })
                                ->where([
                                    ['campaignDetails.adslots', '>', 0]
                                ])->orderBy('campaignDetails.time_created', 'DESC')
                                ->get();
    }

    public function getCampaignDatatables($all_campaigns)
    {
        $campaigns = [];
        foreach ($all_campaigns as $all_campaign)
        {
            $start_date = strtotime($all_campaign->start_date);
            $stop_date = strtotime($all_campaign->stop_date);
            $company_details_service = new CompanyDetails($all_campaign->launched_on);
            $campaigns[] = [
                'id' => $all_campaign->campaign_reference,
                'campaign_id' => $all_campaign->campaign_id,
                'name' => $all_campaign->name,
                'brand' => ucfirst($all_campaign->brand_name),
                'product' => $all_campaign->product,
                'date_created' => date('M j, Y', strtotime($all_campaign->time_created)),
                'start_date' => date('M j, Y', $start_date),
                'end_date' => date('Y-m-d', $stop_date),
                'adslots' => count((explode(',', $all_campaign->adslots_id))),
                'budget' => number_format($all_campaign->total, 2),
                'status' => $all_campaign->status,
                'station' => $company_details_service->getCompanyDetails()->name
            ];
        }

        return $campaigns;

    }

}
