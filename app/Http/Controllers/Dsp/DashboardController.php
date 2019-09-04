<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Services\MediaPlan\GetMediaPlans;
use Vanguard\Models\Campaign;
use Vanguard\Models\CampaignChannel;
use Vanguard\Services\Client\AllClient;
use Vanguard\Services\Client\ClientBrand;
use Vanguard\Libraries\Enum\MediaPlanStatus;

class DashboardController extends Controller
{
    use CompanyIdTrait;

    public function index()
    {
        $media_channels = [
            'TV' => [
                'ratings' => $this->agencyMediaChannel('TV'),
                'icon_url' => asset('new_frontend/img/tv.svg')
            ],
            'Radio' => [
                'ratings' => $this->agencyMediaChannel('Radio'),
                'icon_url' => asset('new_frontend/img/radio.svg')
            ],
            'Newspaper' => [
                'ratings' => [],
                'icon_url' => asset('new_frontend/img/paper.svg')
            ],
            'OOH' => [
                'ratings' => [],
                'icon_url' => asset('new_frontend/img/ooh.svg')
            ],
            'Desktop' => [
                'ratings' => [],
                'icon_url' => asset('new_frontend/img/desktop.svg')
            ],
            'Mobile' => [
                'ratings' => [],
                'icon_url' => asset('new_frontend/img/mobile.svg')
            ],
        ];

        //all clients
        $clients = new AllClient($this->companyId());
        $clients = $clients->getAllClients();
        $count_all_clients = count($clients);

        //all_brands
        $count_all_brands = 0;
        foreach ($clients as $client) {
            $brands = new ClientBrand($client->id);
            $count_all_brands += count($brands->run());
        }

        //Get all campaigns
        $campaigns = Campaign::where('belongs_to', $this->companyId())->get();
        $count_campigns_on_hold = $this->countCampaignsByStatus($campaigns, 'on_hold');
        $count_active_campigns = $this->countCampaignsByStatus($campaigns, 'active');
        $campaign_summary = [
            'count_active_campaigns' => $count_active_campigns,
            'count_campaigns_on_hold' => $count_campigns_on_hold,
            'count_all_brands' => $count_all_brands,
            'count_all_clients' => $count_all_clients,
        ];

        //Get all media plans
        $media_plan_service = new GetMediaPlans('', $this->companyId());
        $media_plans = collect($media_plan_service->run());
        $count_pending_media_plans = $this->countMediaPlanByStatus($media_plans, [MediaPlanStatus::PENDING,MediaPlanStatus::SUGGESTED,MediaPlanStatus::SELECTED,MediaPlanStatus::IN_REVIEW]);
        $count_approved_media_plans = $this->countMediaPlanByStatus($media_plans, [MediaPlanStatus::APPROVED]);
        $count_declined_media_plans = $this->countMediaPlanByStatus($media_plans, [MediaPlanStatus::DECLINED]);
        $media_plan_summary = [
            'count_pending_media_plans' => $count_pending_media_plans,
            'count_approved_media_plans' => $count_approved_media_plans,
            'count_declined_media_plans' => $count_declined_media_plans,
        ];

        //redirect urls
        $redirect_urls = [
            'active_campaigns' => route('agency.campaign.all',['status'=>'active']),
            'campaigns_on_hold' => route('agency.campaign.all',['status'=>'on_hold']),
            'all_clients' => route('clients.list'),
            'all_brands' => route('brand.all'),
            'approved_media_plans' => route('agency.media_plans', ['status'=>'approved']),
            'pending_media_plans' => route('agency.media_plans', ['status'=>'pending']),
            'declined_media_plans' => route('agency.media_plans', ['status'=>'declined']),
        ];

        return view('agency.dashboard.index')->with([
            'redirect_urls' => $redirect_urls,
            'media_channels' => $media_channels,
            'campaign_summary' => $campaign_summary,
            'media_plan_summary' => $media_plan_summary
        ]);
    }

    public function countCampaignsByStatus($campaigns, $status)
    {
        return $campaigns->where('status', $status)->count();
    }

    public function countMediaPlanByStatus($plans, $status)
    {
        return $plans->whereIn('str_status', $status)->count();
    }

    public function agencyMediaChannel($channel)
    {
        $agency_media_channels = [];

        $campaigns = Campaign::where('belongs_to', $this->companyId())->get();

        foreach ($campaigns as $campaign){
            $channels = CampaignChannel::whereIn('id', json_decode($campaign->channel))->first();
            $agency_media_channels[] = [
                'campaign_id' => $campaign->campaign_id,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->stop_date,
                'channel' => $channels->channel,
                'channel_id' => $channels->id,
                'campaign_status' => $campaign->status
            ];

        }

        return $this->calculateChannelPercentageOnAgency($agency_media_channels);
    }

    public function calculateChannelPercentageOnAgency($agency_media_channels)
    {
        $finished_campaigns = [];
        $active_campaigns = [];
        $pending_campaigns = [];
        foreach ($agency_media_channels as $agency_media_channel){
            if($agency_media_channel['campaign_status'] === 'expired'){
                $finished_campaigns[] = $agency_media_channel;
            }else if($agency_media_channel['campaign_status'] === 'pending'){
                $pending_campaigns[] = $agency_media_channel;
            }else {
                $active_campaigns[] = $agency_media_channel;
            }
        }

        //maths to calculate the percentage values
        $total_for_active = count($active_campaigns);
        $total_pending = count($pending_campaigns);
        $total_finished = count($finished_campaigns);
        $total_campaigns_tv = count($agency_media_channels);

        //percentage values
        $percentage_active = $total_for_active != 0 ? ($total_for_active / $total_campaigns_tv) * 100 : 0;
        $percentage_finished = $total_finished != 0 ? ($total_finished / $total_campaigns_tv) * 100 : 0;
        $percentage_pending = $total_pending !=0 ? ($total_pending / $total_campaigns_tv) * 100 : 0;

        return (['percentage_active' => $percentage_active, 'percentage_finished' => $percentage_finished, 'percentage_pending' => $percentage_pending]);
    }
}