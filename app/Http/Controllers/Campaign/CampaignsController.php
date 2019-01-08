<?php

namespace Vanguard\Http\Controllers\Campaign;

use Vanguard\Http\Controllers\Controller;
use Session;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\Campaigns\CampaignGeneralInformationRequest;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\Adslot\AdslotFilterResult;
use Vanguard\Services\Broadcaster\BroadcasterDetails;
use Vanguard\Services\Campaign\DeleteTemporaryUpload;
use Vanguard\Services\Campaign\StoreCampaignGeneralInformation;
use Vanguard\Services\CampaignChannels\Radio;
use Vanguard\Services\CampaignChannels\Tv;
use Vanguard\Services\Client\AllClient;
use Vanguard\Services\Campaign\AllCampaign;
use Vanguard\Services\Client\ClientBrand;
use Vanguard\Services\Industry\IndustryAndSubindustry;
use Vanguard\Services\PreloadedData\PreloadedData;
use Vanguard\Services\Upload\MediaUploadDetails;
use Vanguard\Services\Upload\MediaUploadProcessing;
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
        $campaigns = new AllCampaign($request, $this->utilities, $this->dataTables, $broadcaster_id, $agency_id, $dashboard = false);
        return $campaigns->run();
    }

    public function campaignGeneralInformation()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        $preloaded_data = new PreloadedData();
        $client_brand = '';
        $campaign_general_information = Session::get('campaign_information');
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
                ->with('channels', $preloaded_data->getCampaignChannels())
                ->with('sub_industries', $preloaded_data->getSubsectors());

    }

    public function storeCampaignGeneralInformation(CampaignGeneralInformationRequest $request)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        if($request->min_age < 0 || $request->max_age < 0){
            Session::flash('error', ClassMessages::NEGATIVE_AGE_MESSAGE);
            return back();
        }elseif ($request->min_age > $request->max_age){
            Session::flash('error', ClassMessages::AGE_ERROR_MESSAGE);
            return back();
        }elseif (strtotime($request->end_date) < strtotime($request->start_date)){
            Session::flash('error', ClassMessages::DATE_ERROR_MESSAGE);
            return redirect()->back();
        }
        $campaign_general_information = new StoreCampaignGeneralInformation($request);
        $user_id = $campaign_general_information->run();
        $delete_temporary_uploads = new DeleteTemporaryUpload($broadcaster_id, $agency_id, $user_id);
        $delete_temporary_uploads->run();
        return redirect()->route('campaign.advert_slot', ['id' => $user_id])
                        ->with('id', $user_id);
    }

    public function getBrandsIndustryAndSubIndustry()
    {
        $brand_industry_subindustry = new IndustryAndSubindustry(request()->brand);
        $brand_industry_subindustry = $brand_industry_subindustry->getBrandIndustryAndSubIndustry();
        return response()->json($brand_industry_subindustry);
    }

    public function getAdSlotResult($id)
    {
        /**
         * Might wanna come back and refactor this method so as to extract the broadcaster_id,
         * agency_id, campaign_general_information into the constructor and also put the check_campaign_information
         * and the if block into another method
         */
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        $campaign_general_information = Session::get('campaign_information');
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        $adslots_filter_result = new AdslotFilterResult($campaign_general_information, $broadcaster_id, $agency_id);
        $adslots_filter_result = $adslots_filter_result->adslotFilterResult();
        return view('campaigns.advert_slot_result')
                    ->with('adslots_filter_result', $adslots_filter_result)
                    ->with('id', $id);

    }

    public function getMediaContent($id)
    {
        /**
         * Might wanna come back and refactor this method so as to extract the broadcaster_id,
         * agency_id, campaign_general_information into the constructor and also put the check_campaign_information
         * and the if block into another method
         */
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        $campaign_general_information = Session::get('campaign_information');
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        $adslots_filter_result = new AdslotFilterResult($campaign_general_information, $broadcaster_id, $agency_id);
        $adslots_filter_result = $adslots_filter_result->adslotFilterResult();
        if(count($adslots_filter_result) === 0){
            Session::flash('error', ClassMessages::EMPTY_ADSLOT_RESULT_FROM_FILTER);
            return redirect()->back();
        }

        $broadcaster_details = new BroadcasterDetails($broadcaster_id);
        $broadcaster_details = $broadcaster_details->getBroadcasterDetails();

        $tv_details_and_uploads = $this->utilities->getTvDetailsAndUploads($id);

        $radio_details_and_uploads = $this->utilities->getRadioDetailsAndUploads($id);

        //going by defensive programming
        if($radio_details_and_uploads['radio']->channel != 'Radio' && $tv_details_and_uploads['tv']->channel != 'TV'){
            Session::flash('error', ClassMessages::FIRST_CHANNEL_ERROR);
            return redirect()->back();
        }

        return view('campaigns.media_content')
                    ->with('id', $id)
                    ->with('broadcaster_details', $broadcaster_id ? $broadcaster_details : '')
                    ->with('radio', $radio_details_and_uploads['radio'])
                    ->with('tv', $tv_details_and_uploads['tv'])
                    ->with('tv_uploads', $tv_details_and_uploads['tv_upload_details'])
                    ->with('radio_uploads', $radio_details_and_uploads['radio_upload_details'])
                    ->with('campaign_general_information', $campaign_general_information);
    }

    public function storeMediaContent($id)
    {
        $media_upload = new MediaUploadProcessing(request(), '', '');
        $media_upload = $media_upload->run();
        if($media_upload === 'error'){
            return response()->json(['error' => 'error']);
        }elseif($media_upload === 'error_number'){
            return response()->json(['error_number' => 'error_number']);
        }elseif($media_upload === 'error_check_image'){
            return response()->json(['error_check_image' => 'error_check_image']);
        }elseif($media_upload === 'success'){
            return response()->json(['success' => 'success']);
        }
    }

    public function removeMediaContent($client_id, $upload_id)
    {
        $delete_media = new MediaUploadProcessing('',$client_id, $upload_id);
        $delete_media = $delete_media->deleteUploadedMedia();
        if($delete_media == 'success'){
            Session::flash('success', ClassMessages::FILE_DELETE_SUCCESS_MESSAGE);
            return back();
        }else{
            Session::flash('error', ClassMessages::FILE_DELETE_ERROR_MESSAGE);
            return back();
        }
    }


}
