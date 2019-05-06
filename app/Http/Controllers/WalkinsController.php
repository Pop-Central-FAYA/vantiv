<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\WalkinStoreRequest;
use Vanguard\Http\Requests\WalkinUpdateRequest;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Services\Brands\BrandDetails;
use Vanguard\Services\Brands\ClientBrand;
use Vanguard\Services\Brands\ClientsBrandWithCampaign;
use Vanguard\Services\Brands\CreateBrand;
use Vanguard\Services\Brands\CreateBrandClient;
use Vanguard\Services\Client\ClientCampaigns;
use Vanguard\Services\Client\ClientDetails;
use Vanguard\Services\Industry\IndustryList;
use Vanguard\Services\Industry\SubIndustryList;
use Vanguard\Services\User\CreateUser;
use Vanguard\Services\User\UpdateUser;
use Vanguard\Services\User\UserDetails;
use Vanguard\Services\Walkin\CreateWalkIns;
use Vanguard\Libraries\Utilities;
use Session;
use Vanguard\Services\Walkin\UpdateWalkIns;
use Vanguard\Services\Walkin\WalkInLists;
use Vanguard\Services\Client\ClientBrand as ClientBrands;

class WalkinsController extends Controller
{
    //NB: agency_id is assumed to be the broadcaster user
    private $broadcaster_id;
    private $agency_id;
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->broadcaster_id = Session::get('broadcaster_id');
            $this->agency_id = Session::get('agency_id');
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $walkins_list_service = new WalkInLists(\Auth::user()->company_id);
        $walkins = $walkins_list_service->getWalkInList();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($walkins);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('list');
        $industries = new IndustryList();
        return view('broadcaster_module.walk-In.index')->with('clients', $entries)->with('industries', $industries->industryList());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getSubIndustry()
    {
        $industry = request()->industry;

        $sud_industry = Utilities::switch_db('api')->select("
            SELECT * FROM subSectors
            WHERE sector_id = '$industry'
            ORDER BY `name` ASC
        ");

        if(count($sud_industry) > 0){
            return $sud_industry;
        }else{
            return response()->json(['error' => 'error']);
        }
    }

    public function store(WalkinStoreRequest $request)
    {
        if($this->broadcaster_id){
            $client_id = $this->broadcaster_id;
        }else{
            $client_id = $this->agency_id;
        }
        $company_id = \Auth::user()->companies->first()->id;
        $brand_slug = str_slug($request->brand_name);
        $client_brands = new ClientBrand($brand_slug, $client_id);
        if($client_brands->checkForBrandExistence() == 'brand_exist'){
            Session::flash('error', ClassMessages::BRAND_ALREADY_EXIST);
            return redirect()->back();
        }
        try{
            \DB::transaction(function () use($request, $brand_slug, $client_id, $company_id) {
                $user_service = new CreateUser($request->first_name,$request->last_name,$request->email, $request->username,
                    $request->phone, '', 'walkins' );
                $user = $user_service->createUser();

                $walkin_service = new CreateWalkIns($user->id, $this->broadcaster_id, $this->agency_id, $request->company_logo,
                    $request->client_type_id, $request->address,$request->company_name, $request->broadcaster_id, $company_id);
                $store_walkin = $walkin_service->createWalkIn();

                $brands = new BrandDetails(null, $brand_slug);
                $brand_details = $brands->getBrandDetails();
                if(!$brand_details){
                    $store_brand_service = new CreateBrand($request->brand_name, $request->company_logo, $request->industry,
                        $request->sub_industry, $brand_slug);
                    $store_brand = $store_brand_service->storeBrand();

                    $store_brand_client_service = new CreateBrandClient($this->broadcaster_id,$store_brand->id,$client_id,$store_walkin->id);
                    $store_brand_client_service->storeClientBrand();
                }else{
                    $store_brand_client_service = new CreateBrandClient($this->broadcaster_id,$brand_details->id,$client_id,$store_walkin->id);
                    $store_brand_client_service->storeClientBrand();
                }
            });
        }catch (\Exception $exception){
            Session::flash('error', ClassMessages::WALKIN_ERROR);
            return redirect()->back();
        }

        if($this->broadcaster_id){
            return redirect()->route('walkins.all');
        }else{
            return redirect()->route('clients.list');
        }

    }

    public function updateWalKins(WalkinUpdateRequest $request, $client_id)
    {
        $walkin_details_service = new ClientDetails($client_id, null);
        $walkin_details = $walkin_details_service->run();
        try{
            \DB::transaction(function () use ($walkin_details, $client_id, $request) {
                $update_walkin_service = new UpdateWalkIns($request->company_logo, $request->company_name, $request->address, $client_id);
                $update_walkin_service->updateWalkIns();
                $update_user_service = new UpdateUser($walkin_details->user_id, $request->first_name, $request->last_name, $request->phone,
                    null, null, null, 'walkins_update', null);
                $update_user_service->updateUser();
            });
        }catch (\Exception $exception){
            Session::flash('error', ClassMessages::UPDATE_WALKINS_ERROR);
            return redirect()->back();
        }

        Session::flash('success', ClassMessages::UPDATE_WALKINS_SUCCESS);
        return redirect()->back();
    }

    public function getDetails($client_id)
    {
        $client_details_data = $this->clientDetailsData($client_id, $this->getCompanyIdsList());
        $brands_campaign_service = new ClientsBrandWithCampaign($client_id, $this->getCompanyIdsList());
        $brands = $this->getBrandDetails($brands_campaign_service->getClientsBrandWithCampaigns());
        if(count($brands) === 0){
            Session::flash('info', ClassMessages::EMPTY_BRAND_FOR_CLIENT);
            return redirect()->back();
        }

        return view('broadcaster_module.walk-In.details')->with('clients')
            ->with('client_id', $client_id)
            ->with('user_details', $client_details_data['user_details']->getUserDetails())
            ->with('client', $client_details_data['client'])
            ->with('all_campaigns', $client_details_data['all_campaigns'])
            ->with('all_brands', $brands)
            ->with('campaign_payment', $client_details_data['campaign_payments'])
            ->with('campaign_date', $client_details_data['campaign_date'])
            ->with('total_spent', $client_details_data['client_campaign_service']->getClientTotalSpent())
            ->with('industries', $client_details_data['industries']->industryList())
            ->with('sub_industries', $client_details_data['sub_industries']->getSubIndustryGroupByIndustry())
            ->with('publisher_logos', \Auth::user()->companies()->count() > 1 ? $client_details_data['publisher_logo'] : '')
            ->with('publisher_ids', \Auth::user()->companies()->count() > 1 ? $client_details_data['publisher_id'] : '');
    }

    public function filterByPublisher($client_id)
    {
        $channel_id = request()->channel_id;
        //$client_id = request()->client_id;
        $client_details_data = $this->clientDetailsData($client_id, $channel_id);
        $brands_campaign_service = new ClientsBrandWithCampaign($client_id, $channel_id);
        $brands = $this->getBrandDetails($brands_campaign_service->getClientsBrandWithCampaigns());
        if(count($brands) === 0){
            return 'empty_brand';
        }
        return ['client_id' => $client_id, $channel_id, 'user_details' => $client_details_data['user_details']->getUserDetails(), 'client' => $client_details_data['client'],
            'all_campaigns' => $client_details_data['all_campaigns'], 'all_brands' => $brands, 'campaign_payment' => $client_details_data['campaign_payments'],
            'campaign_date' => $client_details_data['campaign_date'], 'total_spent' => $client_details_data['client_campaign_service']->getClientTotalSpent(),
            'industries' => $client_details_data['industries']->industryList(), 'sub_industries' => $client_details_data['sub_industries']->getSubIndustryGroupByIndustry(),
            'publisher_logos' => \Auth::user()->companies()->count() > 1 ? $client_details_data['publisher_logo'] : '',
            'publisher_ids' => \Auth::user()->companies()->count() > 1 ? $client_details_data['publisher_id'] : ''];
    }

    public function clientDetailsData($client_id, $company_id)
    {
        $client_details_service = new ClientDetails($client_id, null);
        $client_details = $client_details_service->run();
        $client_campaign_service = new ClientCampaigns($client_details->id, $company_id);
        $client_campaigns = $client_campaign_service->getComprehensiveDetails();
        $client_brands_service = new ClientBrands($client_id);
        $client_brands = $client_brands_service->run();
        $user_details = new UserDetails($client_details->user_id);
        $industries = new IndustryList();
        $sub_industries = new SubIndustryList();
        //        campaign vs time graph
        $campaign_graph = $this->clientGraph($client_campaigns);
        $campaign_payment = $campaign_graph['campaign_payment'];
        $campaign_date = $campaign_graph['campaign_date'];
        $campaign_publishers_logo = '';
        $campaign_publishers_id = '';
        if(\Auth::user()->companies()->count() > 1){
            $campaign_publishers = $client_campaign_service->getPublishers();
            if(count($campaign_publishers) != 0){
                $campaign_publishers_logo = explode(',', $campaign_publishers[0]->company_logo);
                $campaign_publishers_id = explode(',', $campaign_publishers[0]->company_id);
            }
        }
        return ['user_details' => $user_details, 'client' => $client_details, 'all_campaigns' => $client_campaigns,
            'all_brands' => $client_brands, 'campaign_payments' => $campaign_payment, 'campaign_date' => $campaign_date,
            'client_campaign_service' => $client_campaign_service, 'industries' => $industries, 'sub_industries' => $sub_industries,
            'publisher_logo' => $campaign_publishers_logo, 'publisher_id' => $campaign_publishers_id, 'client_details_service' => $client_details_service];
    }

    public function getBrandDetails($client_brands)
    {
        $brands = [];
        foreach ($client_brands as $client_brand){
            $brands[] = [
                'id' => $client_brand->id,
                'client_id' => $client_brand->client_walkins_id,
                'brand' => $client_brand->brand,
                'date' => $client_brand->date,
                'count_brand' => count($client_brands),
                'campaigns' => $client_brand->campaigns,
                'image_url' => $client_brand->image_url,
                'last_campaign' =>  $client_brand->last_campaign ? $client_brand->last_campaign : 'none',
                'total' => number_format($client_brand->total,2),
                'industry_id' => $client_brand->industry_id,
                'sub_industry_id' => $client_brand->sub_industry_id,
            ];
        }
        return $brands;
    }

    public function clientGraph($campaigns)
    {
        $all_campaign_total_graph = [];
        $all_campaign_date_graph = [];

        foreach ($campaigns as $campaign){
            $all_campaign_total_graph[] = $campaign->total_on_graph;
            $all_campaign_date_graph[] = date('Y-m-d', strtotime($campaign->time_created));
        }

        $campaign_payment = json_encode($all_campaign_total_graph);
        $campaign_date = json_encode($all_campaign_date_graph);

        return (['campaign_payment' => $campaign_payment, 'campaign_date' => $campaign_date]);
    }


}
