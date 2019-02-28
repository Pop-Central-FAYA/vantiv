<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CampaignStatus;
use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Services\Company\CompanyDetails;
use Vanguard\Services\Traits\CampaignQueryTrait;
use Yajra\DataTables\DataTables;

class AllCampaign
{
    protected $request;
    protected $dashboard;
    protected $company_ids;

    use CampaignQueryTrait;

    public function __construct($request, $dashboard, $company_ids)
    {
        $this->request = $request;
        $this->dashboard = $dashboard;
        $this->company_ids = $company_ids;
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
                if(\Auth::user()->company_type == CompanyTypeName::BROADCASTER){
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
        return  $this->campaignBaseQuery()
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
                                ->when(\Auth::user()->company_type == CompanyTypeName::BROADCASTER && is_array($this->company_ids), function($query) {
                                    return $query->selectRaw("JSON_ARRAYAGG(campaignDetails.launched_on) AS station_id")
                                                    ->whereIn('campaignDetails.launched_on', $this->company_ids)
                                                    ->groupBy('campaignDetails.campaign_id');
                                })
                                ->when(\Auth::user()->company_type == CompanyTypeName::BROADCASTER && !is_array($this->company_ids), function($query) {
                                    return $query->where([
                                                    ['campaignDetails.broadcaster', $this->company_ids],
                                                    ['campaignDetails.adslots', '>', 0],
                                                    ['paymentDetails.broadcaster', $this->company_ids]
                                                ]);
                                })
                                ->when(\Auth::user()->company_type == CompanyTypeName::AGENCY, function ($query) {
                                    return $query->where([
                                                    ['campaignDetails.agency', $this->company_ids],
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
                                    return $query->when(is_array($this->company_ids), function ($inner_query) {
                                                    return $inner_query->selectRaw("JSON_ARRAYAGG(campaignDetails.launched_on) AS station_id")
                                                                        ->whereIn('campaignDetails.agency_broadcaster', $this->company_ids)
                                                                        ->whereIn('campaignDetails.launched_on', $this->company_ids)
                                                                        ->groupBy('campaignDetails.campaign_id');
                                                })
                                                ->when(!is_array($this->company_ids), function ($inner_query) {
                                                    return $inner_query->where([
                                                                ['campaignDetails.agency_broadcaster', $this->company_ids],
                                                                ['paymentDetails.broadcaster', $this->company_ids]
                                                            ]);
                                                });
                                })
                                ->when(($this->request->filter_user == 'broadcaster'), function($query) use ($broadcaster_id) {
                                    return $query->when(is_array($this->company_ids), function($inner_query) {
                                                return $inner_query->selectRaw("JSON_ARRAYAGG(campaignDetails.launched_on) AS station_id")
                                                                    ->where('campaignDetails.agency', '')
                                                                    ->whereIn('campaignDetails.belongs_to', $this->company_ids)
                                                                    ->groupBy('campaignDetails.campaign_id');
                                            })
                                            ->when(!is_array($this->company_ids), function ($inner_query) {
                                                return $inner_query->where([
                                                    ['campaignDetails.broadcaster', $this->company_ids],
                                                    ['campaignDetails.agency', ''],
                                                    ['paymentDetails.broadcaster', $this->company_ids]
                                                ]);
                                            });
                                })
                                ->when(\Request::is('campaign/active-campaigns'), function($query){
                                    return $query->where('campaignDetails.status', CampaignStatus::ACTIVE_CAMPAIGN);
                                })
                                ->where([
                                    ['campaignDetails.adslots', '>', 0]
                                ])
                                ->orderBy('campaignDetails.time_created', 'DESC')
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
                'adslots' => $this->countAdslots($all_campaign),
                'budget' => $this->totalSpentOnCampaign($all_campaign),
                'status' => $all_campaign->status,
                'station' => \Auth::user()->companies()->count() > 1 ? $this->getCompanyName($all_campaign->station_id) : ''
            ];
        }

        return $campaigns;

    }

    public function totalSpentOnCampaign($all_campaign)
    {
        if(\Auth::user()->companies()->count() > 1 && \Auth::user()->company_type == CompanyTypeName::BROADCASTER){
            $total = number_format($all_campaign->total, 2);
        }elseif (\Auth::user()->company_type == CompanyTypeName::AGENCY){
            $total = number_format($all_campaign->total, 2);
        }else{
            $total = number_format($all_campaign->individual_broadcaster_sum, 2);
        }
        return $total;
    }

    public function countAdslots($all_campaign)
    {
        if(\Auth::user()->companies()->count() > 1 && \Auth::user()->company_type == CompanyTypeName::BROADCASTER){
            $count_adslots = count((explode(',', $all_campaign->adslots_id)));
        }elseif (\Auth::user()->company_type == CompanyTypeName::AGENCY){
            $count_adslots = count((explode(',', $all_campaign->adslots_id)));
        }else{
            $count_adslots = $all_campaign->adslots;
        }
        return $count_adslots;
    }

    public function getCompanyName($company_id)
    {
        $companies = \DB::table('companies')
                        ->whereIn('id', json_decode($company_id))
                        ->get();
        $company_name = '';
        foreach ($companies as $company){
            $company_name .= $company->name.' ';
        }
        return $company_name;
    }

}
