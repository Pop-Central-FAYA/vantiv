<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Utilities;

class AllCampaign
{
    protected $broadcaster_id;
    protected $agency_id;
    protected $request;
    protected $utilities;
    protected $dataTables;
    protected $dashboard;

    public function __construct($request, $utilities, $dataTables, $broadcaster_id, $agency_id, $dashboard)
    {
        $this->broadcaster_id = $broadcaster_id;
        $this->agency_id = $agency_id;
        $this->request = $request;
        $this->utilities = $utilities;
        $this->dataTables = $dataTables;
        $this->dashboard = $dashboard;
    }

    public function run()
    {
        return $this->campaignsDataToDatatables($this->dataTables);
    }

    public function campaignsDataToDatatables($dataTables)
    {

        $campaigns = $this->fetchAllCampaigns();

        $campaigns = $this->utilities->getCampaignDatatables($campaigns);

        return $dataTables->collection($campaigns)
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

    public function allCampaigns()
    {
        return Utilities::switch_db('api')->table('campaignDetails')
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
                                                     'payments.total',
                                                     'brands.name AS brand_name',
                                                     'campaigns.campaign_reference'
                                    )
                                ->when($this->request->start_date && $this->request->stop_date, function ($query) {
                                    return $query->between('campaignDetails.start_date', $this->request->start_date,
                                                    $this->request->stop_date);
                                })
                                ->when(!$this->dashboard, function($query){
                                    return $query->where('campaignDetails.status', CampaignStatus::ACTIVE_CAMPAIGN);
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
        return Utilities::switch_db('api')->table('campaignDetails')
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
                'payments.total',
                'brands.name AS brand_name',
                'campaigns.campaign_reference'
            )
            ->when($this->request->start_date && $this->request->stop_date, function ($query) {
                return $query->between('campaignDetails.start_date', $this->request->start_date,
                    $this->request->stop_date);
            })
            ->when(($this->request->filter_user == 'agency'), function ($query) {
                return $query->where('campaignDetails.agency_broadcaster', $this->broadcaster_id);
            })
            ->when(($this->request->filter_user == 'broadcaster'), function($query) {
                return $query->where([
                    ['campaignDetails.broadcaster', $this->broadcaster_id],
                    ['campaignDetails.agency', '']
                ]);
            })
            ->when(\Request::is('campaign/active-campaigns'), function($query){
                return $query->where('campaignDetails.status', CampaignStatus::ACTIVE_CAMPAIGN);
            })
            ->where([
                ['campaignDetails.adslots', '>', 0]
            ])->orderBy('campaignDetails.time_created', 'DESC')
            ->get();
    }

}
