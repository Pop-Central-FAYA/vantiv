<?php

namespace Vanguard\Services\Campaign;

use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Services\Traits\CampaignQueryTrait;
use Yajra\DataTables\DataTables;

use Vanguard\Models\Company;

use Log;

class CampaignList
{
    protected $request;
    protected $company_ids;

    use CampaignQueryTrait;

    /**
     * The variables to filter by are:
     * 1. status
     * 2. start_date and end_date
     * 3. campaign_type
     * 4. keyword (this is probably frontend only)
     * 5. eventually, also company_ids
     */
    public function __construct($request, $company_ids)
    {
        $this->request = $request;
        $this->company_ids = $company_ids;
        if (!is_array($this->company_ids)) {
            $this->company_ids = array($this->company_ids);
        }
        $this->user_company_type = \Auth::user()->company_type;
        $this->user_companies = \Auth::user()->companies()->count();
    }

    public function run()
    {
        return $this->campaignsDataToDatatables();
    }

    protected function campaignsDataToDatatables()
    {
        $campaigns = $this->fetchAllCampaigns();
        $campaigns = $this->getCampaignDatatables($campaigns);

        $datatables = new DataTables();
        return $datatables->collection($campaigns)
            ->addColumn('name', function ($campaigns) {
                if($this->isBroadcasterUser()) {
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
        $query = $this->campaignBaseQuery()->when($this->hasDateFilter(), function ($query) {
            // the relevant filter here is the date filter
            $between = array($this->request->start_date, $this->request->stop_date);
            return $query->whereBetween('campaignDetails.start_date', $between);
        })->when($this->request->status, function($query) {
            // if the filter is by status
            $status = $this->request->status;
            if (!is_array($this->request->status)) {
                $status = array($status);
            }
            return $query->whereIn('campaignDetails.status', $status);
        })->when($this->request->filter_user, function($query) {
            // if the filter is by campaign type
            // only return campaigns that were either launched by agencies or walkins by the campaign
            if ($this->request->filter_user == 'agency') {
                return $query->where('campaignDetails.agency_broadcaster', '<>', '');
            } 
            if ($this->request->filter_user == 'broadcaster') {
                return $query->where('campaignDetails.agency_broadcaster', '');
            }
        })->when($this->isAgencyUser(), function($query) {
            // if this is a request from an agency
            return $query->whereIn('campaignDetails.agency', $this->company_ids)
                ->where('campaignDetails.adslots', '>', 0)
                ->groupBY('campaignDetails.campaign_id');
        })->when($this->isBroadcasterUser(), function($query) {
            // if this is a request from a broadcaster
            return $query->selectRaw("JSON_ARRAYAGG(campaignDetails.launched_on) AS station_id")
                ->whereIn('campaignDetails.launched_on', $this->company_ids)
                ->where('campaignDetails.adslots', '>', 0)
                ->whereIn('paymentDetails.broadcaster', $this->company_ids)
                ->groupBy('campaignDetails.campaign_id');
        })->orderBy('campaignDetails.time_created', 'DESC');
        return $query->get();
    }

    protected function hasDateFilter() 
    {
        return ($this->request->start_date && $this->request->stop_date);
    }

    protected function isAgencyUser()
    {
        return $this->user_company_type === CompanyTypeName::AGENCY;
    }

    protected function isBroadcasterUser()
    {
        return $this->user_company_type === CompanyTypeName::BROADCASTER;
    }

    protected function getCampaignDatatables($all_campaigns)
    {
        return $all_campaigns->map(function($item) {
            $start_date = strtotime($item->start_date);
            $stop_date = strtotime($item->stop_date);
            return array(
                'id' => $item->campaign_reference,
                'campaign_id' => $item->campaign_id,
                'name' => $item->name,
                'brand' => ucfirst($item->brand_name),
                'product' => $item->product,
                'date_created' => date('M j, Y', strtotime($item->time_created)),
                'start_date' => date('M j, Y', $start_date),
                'end_date' => date('Y-m-d', $stop_date),
                'adslots' => $this->countAdslots($item),
                'budget' => $this->totalSpentOnCampaign($item),
                'status' => $item->status,
                'station' => $this->getStations($item->station_id)
            );
        });
    }

    protected function totalSpentOnCampaign($all_campaign)
    {
        if($this->isAgencyOrBroadcasterWithMultipleCompanies()) {
            $total = number_format($all_campaign->total, 2);
        }else{
            $total = number_format($all_campaign->individual_broadcaster_sum, 2);
        }
        return $total;
    }

    protected function countAdslots($all_campaign)
    {
        if ($this->isAgencyOrBroadcasterWithMultipleCompanies()) {
            $count_adslots = count((explode(',', $all_campaign->adslots_id)));
        }else{
            $count_adslots = $all_campaign->adslots;
        }
        return $count_adslots;
    }

    protected function getStations($company_id)
    {
        $company_id_list = json_decode($company_id);
        if ($company_id_list) {
            $companies = Company::select('name')->whereIn('id', $company_id_list)->get();
            return $companies->implode("name", ", ");
        }
        return "";
    }

    protected function hasMultipleCompanies() {
        return $this->user_companies > 1;
    }

    protected function isAgencyOrBroadcasterWithMultipleCompanies() {
        return ($this->isAgencyUser() || ($this->hasMultipleCompanies() && $this->isBroadcasterUser()));
    }

}
