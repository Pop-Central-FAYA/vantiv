<?php

namespace Vanguard\Http\Controllers\Campaign;

use Vanguard\Http\Controllers\Controller;
use Session;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\Campaign\AllCampaignService;
use Vanguard\Services\PreloadedData;
use Yajra\DataTables\DataTables;

class CampaignsController extends Controller
{
    protected $utilities;
    protected $dataTables;

    public function __construct(Utilities $utilities, DataTables $dataTables)
    {
        $this->utilities = $utilities;
        $this->dataTables = $dataTables;
    }

    public function allActiveCampaigns()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        if($broadcaster_id){
            return view('broadcaster_module.campaigns.index');
        }else{
            return view('agency.campaigns.active_campaign');
        }
    }

    public function allActiveCampaignsData(Request $request)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        $campaigns = new AllCampaignService($request, $this->utilities, $this->dataTables, $broadcaster_id, $agency_id, $dashboard = false);
        return $campaigns->run();
    }

    public function campaignGeneralInformation()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $preloaded_data = new PreloadedData();
        if($broadcaster_id){
            return view('broadcaster_module.campaigns.create_step1')
                    ->with('industries', $preloaded_data->getSectors())
                    ->with('day_parts', $preloaded_data->getDayParts())
                    ->with('sub_industries', $preloaded_data->getSubsectors());
        }else{

        }
    }

    public function storeCampaignGeneralInformation()
    {

    }
}
