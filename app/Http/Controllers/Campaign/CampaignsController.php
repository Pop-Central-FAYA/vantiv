<?php

namespace Vanguard\Http\Controllers\Campaign;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Controller;
use Session;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\Campaigns\CampaignGeneralInformationRequest;
use Vanguard\Libraries\CampaignDate;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\Adslot\AdslotFilterResult;
use Vanguard\Services\Adslot\PreselectedAdslotService;
use Vanguard\Services\Adslot\RatecardService;
use Vanguard\Services\Campaign\DeleteTemporaryUpload;
use Vanguard\Services\Campaign\StoreCampaignGeneralInformation;
use Vanguard\Services\Client\AllClient;
use Vanguard\Services\Campaign\AllCampaign;
use Vanguard\Services\Client\ClientBrand;
use Vanguard\Services\FilePosition\Fileposition;
use Vanguard\Services\Industry\IndustryAndSubindustry;
use Vanguard\Services\PreloadedData\PreloadedData;
use Vanguard\Services\Upload\MediaUploadProcessing;
use Yajra\DataTables\DataTables;

class CampaignsController extends Controller
{
    protected $utilities;
    protected $dataTables;
    private $broadcaster_id;
    private $agency_id;
    private $campaign_general_information;
    protected $campaign_date;

    public function __construct(Utilities $utilities, DataTables $dataTables, CampaignDate $campaign_date)
    {
        $this->utilities = $utilities;
        $this->dataTables = $dataTables;
        $this->campaign_date = $campaign_date;
        $this->middleware(function ($request, $next) {
            $this->broadcaster_id = Session::get('broadcaster_id');
            $this->agency_id = Session::get('agency_id');
            $this->campaign_general_information = Session::get('campaign_information');
            return $next($request);
        });
    }

    public function allActiveCampaigns()
    {
        if($this->broadcaster_id){
            return view('broadcaster_module.campaigns.index');
        }else{
            return view('agency.campaigns.active_campaign');
        }
    }

    public function allActiveCampaignsData(Request $request)
    {
        $campaigns = new AllCampaign($request, $this->utilities, $this->dataTables, $this->broadcaster_id, $this->agency_id, $dashboard = false);
        return $campaigns->run();
    }

    public function campaignGeneralInformation()
    {
        $preloaded_data = new PreloadedData();
        $client_brand = '';
        if($this->campaign_general_information){
            $client_brand = new ClientBrand($this->campaign_general_information->client);
            $client_brand = $client_brand->run();
        }
        $clients = new AllClient($this->broadcaster_id, $this->agency_id);
        $clients = $clients->getAllClients();
        return view('campaigns.campaign_general_information')
                ->with('industries', $preloaded_data->getSectors())
                ->with('day_parts', $preloaded_data->getDayParts())
                ->with('regions', $preloaded_data->getRegions())
                ->with('brands', $client_brand)
                ->with('clients', $clients)
                ->with('targets', $preloaded_data->getTargetAudiences())
                ->with('campaign_general_information', $this->campaign_general_information)
                ->with('channels', $preloaded_data->getCampaignChannels())
                ->with('sub_industries', $preloaded_data->getSubsectors());

    }

    public function storeCampaignGeneralInformation(CampaignGeneralInformationRequest $request)
    {
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
        $delete_temporary_uploads = new DeleteTemporaryUpload($this->broadcaster_id, $this->agency_id, $user_id);
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
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        return view('campaigns.advert_slot_result')
                    ->with('adslots_filter_result', $this->adslotResultDetails())
                    ->with('id', $id);

    }

    public function getMediaContent($id)
    {
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        if(count($this->adslotResultDetails()) === 0){
            Session::flash('error', ClassMessages::EMPTY_ADSLOT_RESULT_FROM_FILTER);
            return redirect()->back();
        }

        $tv_details_and_uploads = $this->utilities->getTvDetailsAndUploads($id);
        $radio_details_and_uploads = $this->utilities->getRadioDetailsAndUploads($id);
        //going by defensive programming
        if($radio_details_and_uploads['radio']->channel != 'Radio' && $tv_details_and_uploads['tv']->channel != 'TV'){
            Session::flash('error', ClassMessages::FIRST_CHANNEL_ERROR);
            return redirect()->back();
        }

        return view('campaigns.media_content')
                    ->with('id', $id)
                    ->with('broadcaster_details', $this->broadcaster_id ? $this->utilities->getBroadcasterDetails($this->broadcaster_id) : '')
                    ->with('radio', $radio_details_and_uploads['radio'])
                    ->with('tv', $tv_details_and_uploads['tv'])
                    ->with('tv_uploads', $tv_details_and_uploads['tv_upload_details'])
                    ->with('radio_uploads', $radio_details_and_uploads['radio_upload_details'])
                    ->with('campaign_general_information', $this->campaign_general_information);
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

    public function preProcessAdslot($id)
    {
        $first_week = $this->campaign_date->getFirstWeek($this->campaign_general_information->start_date, $this->campaign_general_information->end_date);
        $campaign_start_end_date_of_the_week = $this->campaign_date->getStartAndEndDateForFirstWeek($first_week);
        return redirect()->route('campaign.adslot_selection', ['id' => $id, 'broadcaster' => $this->broadcaster_id,
                                    'start_date' => $campaign_start_end_date_of_the_week['start_date_of_the_week'],
                                    'end_date' => $campaign_start_end_date_of_the_week['end_date_of_the_week']
                                ]);
    }

    public function getAdslotSelection($id, $broadcaster_id, $start_date, $end_date)
    {
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        $preselected_slots_service = new PreselectedAdslotService($id, null, null, null, null);
        $file_position_service = new Fileposition($broadcaster_id);
        $broadcaster_details = $this->utilities->getBroadcasterDetails($broadcaster_id);
        $campaign_week = $this->utilities->getStartAndEndDateWithTheWeek($this->campaign_general_information->start_date,
                                                                        $this->campaign_general_information->end_date);
        $media_details = $this->utilities->getMediaUploadDetails($id, null);
        $preselected_adslots = $preselected_slots_service->getPreselectedSlots();
        $total_price = $preselected_slots_service->getSumPrice();
        $file_positions = $file_position_service->filePositionDetails();
        $rate_card_object = new RatecardService($this->campaign_general_information, $broadcaster_id,
                                                $start_date,$end_date,$broadcaster_details);
        $rate_cards = $rate_card_object->run();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($rate_cards['rate_cards']);
        $perPage = 100;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('/agency/campaigns/campaign/step4/'.$id.'/'.$this->broadcaster_id);

        return view('campaigns.adslot_selection')
            ->with('ratecards', $entries)
            ->with('total_amount', $total_price)
            ->with('preselected_adslots', $preselected_adslots)
            ->with('uploaded_data', $media_details)
            ->with('id', $id)
            ->with('broadcaster', $broadcaster_id)
            ->with('positions', $file_positions)
            ->with('ratings', $rate_cards['adslots'])
            ->with('campaign_dates_by_week', $campaign_week)
            ->with('campaign_general_information', $this->campaign_general_information);

    }

    public function postPreselectedAdslot($id)
    {
        $post_preselected_adslot = new PreselectedAdslotService($id, request()->broadcaster, $this->agency_id, request(),
                                                                $this->campaign_general_information);
        $post_preselected_adslot = $post_preselected_adslot->storePreselectedAdslot();
        if($post_preselected_adslot === 'file_error'){
            return response()->json(['file_error' => 'file_error']);
        }elseif($post_preselected_adslot === 'budget_exceed_error'){
            return response()->json(['budget_exceed_error' => 'budget_exceed_error']);
        }elseif($post_preselected_adslot === 'error'){
            return response()->json(['error' => 'error']);
        }elseif(!$post_preselected_adslot){
            return response()->json(['failure' => 'failure']);
        }else{
            return response()->json(['success' => 'success']);
        }
    }

    public function selectBroadcaster($id)
    {
        $check_campaign_information = $this->utilities->checkCampaignInformationSessionActiveness($this->campaign_general_information);
        if($check_campaign_information === 'data_lost'){
            Session::flash('error', ClassMessages::CAMPAIGN_INFROMATION_SESSION_DATA_LOSS);
            return back();
        }
        $adslot_results = new AdslotFilterResult($this->campaign_general_information, $this->broadcaster_id, $this->agency_id,
                                                $this->campaign_general_information->start_date,$this->campaign_general_information->end_date);
        $campaign_first_week = $this->campaign_date->getFirstWeek($this->campaign_general_information->start_date,
                                                                    $this->campaign_general_information->end_date);
        return view('campaigns.broadcaster_select')
                    ->with('adslot_search_results', $adslot_results->adslotFilterResult())
                    ->with('id', $id)
                    ->with('campaign_first_week', $campaign_first_week);
    }

    public function adslotResultDetails()
    {
        $adslots_filter_result = new AdslotFilterResult($this->campaign_general_information, $this->broadcaster_id,
            $this->agency_id, $this->campaign_general_information->start_date, $this->campaign_general_information->end_date);
        $adslots_filter_result = $adslots_filter_result->adslotFilterResult();
        return $adslots_filter_result;
    }
}
