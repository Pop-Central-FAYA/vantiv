<?php

namespace Vanguard\Http\Controllers\Campaign;

use Vanguard\Http\Controllers\Controller;
use Session;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\Campaigns\CampaignGeneralInformationRequest;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\Campaign\DeleteTemporaryUpload;
use Vanguard\Services\Campaign\StoreCampaignGeneralInformation;
use Vanguard\Services\Client\AllClient;
use Vanguard\Services\Campaign\AllCampaign;
use Vanguard\Services\Client\ClientBrand;
use Vanguard\Services\Industry\IndustryAndSubindustry;
use Vanguard\Services\PreloadedData\PreloadedData;
use Yajra\DataTables\DataTables;

class CampaignsController extends Controller
{
    protected $utilities;
    protected $dataTables;

    const NEGATIVE_AGE_MESSAGE = 'The minimum or maximum age cannot have a negative value';

    const AGE_ERROR_MESSAGE = 'The minimum age cannot be greater than the maximum age';

    const DATE_ERROR_MESSAGE = 'Start Date cannot be greater than End Date';

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
        $campaigns = new AllCampaign($request, $this->utilities, $this->dataTables, $broadcaster_id, $agency_id, $dashboard = false);
        return $campaigns->run();
    }

    public function campaignGeneralInformation()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        $preloaded_data = new PreloadedData();
        $client_brand = '';
        $campaign_general_information = Session::get('campaign_general_information');
        if($campaign_general_information){
            $client_brand = new ClientBrand($campaign_general_information->client);
            $client_brand = $client_brand->run();
        }
        $clients = new AllClient($broadcaster_id, $agency_id);
        $clients = $clients->getAllClients();
        return view('campaigns.campaign_general_information')
                ->with('industries', $preloaded_data->getSectors())
                ->with('day_parts', $preloaded_data->getDayParts())
                ->with('regions', $preloaded_data->getRegions())
                ->with('brands', $client_brand)
                ->with('clients', $clients)
                ->with('targets', $preloaded_data->getTargetAudiences())
                ->with('campaign_general_information', $campaign_general_information)
                ->with('sub_industries', $preloaded_data->getSubsectors());

    }

    public function storeCampaignGeneralInformation(CampaignGeneralInformationRequest $request)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');

        if($request->min_age < 0 || $request->max_age < 0){
            Session::flash('error', self::NEGATIVE_AGE_MESSAGE);
            return back();
        }elseif ($request->min_age > $request->max_age){
            Session::flash('error', self::AGE_ERROR_MESSAGE);
            return back();
        }elseif (strtotime($request->end_date) < strtotime($request->start_date)){
            Session::flash('error', self::DATE_ERROR_MESSAGE);
            return redirect()->back();
        }

        $campaign_general_information = new StoreCampaignGeneralInformation($request);

        $user_id = $campaign_general_information->run();

        $delete_temporary_uploads = new DeleteTemporaryUpload($broadcaster_id, $agency_id, $user_id);
        $delete_temporary_uploads->run();

        return redirect()->route('campaign.advert_slot', ['id' => $user_id])
                        ->with('id', $user_id)
                        ->with('campaign_general_information', Session::get('campaign_general_information'));
    }

    public function getBrandsIndustryAndSubIndustry()
    {
        $brand_industry_subindustry = new IndustryAndSubindustry(request()->brand);
        $brand_industry_subindustry = $brand_industry_subindustry->getBrandIndustryAndSubIndustry();
        return response()->json($brand_industry_subindustry);
    }

    public function getAdSlotResult()
    {

    }
}
