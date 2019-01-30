<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Vanguard\Http\Requests\WalkinStoreRequest;
use Vanguard\Http\Requests\WalkinUpdateRequest;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Services\Brands\BrandCampaigns;
use Vanguard\Services\Brands\BrandDetails;
use Vanguard\Services\Brands\ClientBrand;
use Vanguard\Services\Brands\CreateBrand;
use Vanguard\Services\Brands\CreateBrandClient;
use Vanguard\Services\Client\ClientCampaigns;
use Vanguard\Services\Client\ClientDetails;
use Vanguard\Services\Client\ClientTotalSpent;
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
        $walkins_list_service = new WalkInLists($this->broadcaster_id, $this->agency_id);
        $wlakins = $walkins_list_service->getWalkInList();

        $client_data = $this->getClientDetails($wlakins);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($client_data);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('list');
        $industries = new IndustryList();
        return view('broadcaster_module.walk-In.index')->with('clients', $entries)->with('industries', $industries->industryList());
    }

    public function getClientDetails($clients)
    {
        $client_data = [];

        foreach ($clients as $client) {
            $client_campaign_service = new ClientCampaigns($client->user_id, $this->broadcaster_id, $this->agency_id);
            $client_brand_services = new ClientBrands($client->id);
            $client_brand = $client_brand_services->run();
            $total_spent = new ClientTotalSpent($client->user_id, $this->broadcaster_id, null);
            $client_data[] = [
                'client_id' => $client->id,
                'user_id' => $client->user_id,
                'agency_client_id' => $client->user_id,
                'image_url' => $client->image_url,
                'num_campaign' => $client_campaign_service->countAllClientCampaigns(),
                'total' => $total_spent->getClientTotalSpent(),
                'name' =>  $client->lastname . ' ' . $client->firstname,
                'email' => $client->email,
                'phone_number' => $client->phone_number,
                'first_name' => $client->firstname,
                'last_name' => $client->lastname,
                'created_at' => $client->time_created,
                'last_camp' => $client_campaign_service->countAllClientCampaigns() !== 0 ? $client_campaign_service->getLastCampaign()->time_created : 0,
                'active_campaign' => $client_campaign_service->countActiveCampaigns(),
                'inactive_campaign' => $client_campaign_service->countInactiveCampaigns(),
                'count_brands' => count($client_brand),
                'company_name' => $client->company_name,
                'company_logo' => $client->company_logo,
                'location' => $client->location,
            ];
        }
        return $client_data;
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
        $brand_slug = Utilities::formatString($request->brand_name);
        $client_brands = new ClientBrand($brand_slug, $client_id);
        if($client_brands->checkForBrandExistence() == 'brand_exist'){
            Session::flash('error', ClassMessages::BRAND_ALREADY_EXIST);
            return redirect()->back();
        }
        try{
            Utilities::switch_db('api')->transaction(function () use($request, $brand_slug, $client_id) {
                $user_service = new CreateUser($request->first_name,$request->last_name,$request->email, $request->username,
                    $request->phone, '', 'walkins' );
                $user = $user_service->createUser();

                $walkin_service = new CreateWalkIns($user->id, $this->broadcaster_id, $this->agency_id, $request->company_logo,
                    $request->client_type_id, $request->address,$request->company_name, $request->broadcaster_id);
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
            Utilities::switch_db('api')->transaction(function () use ($walkin_details, $client_id, $request) {
                $update_walkin_service = new UpdateWalkIns($request->company_logo, $request->company_name, $request->address, $client_id);
                $update_walkin_service->updateWalkIns();
                $update_user_service = new UpdateUser($walkin_details->user_id, $request->first_name, $request->last_name, $request->phone);
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
        $client_details_service = new ClientDetails($client_id, null);
        $client_details = $client_details_service->run();

        $client_campaign_service = new ClientCampaigns($client_details->user_id, $this->broadcaster_id, null);
        $client_campaigns = $client_campaign_service->getComprehensiveDetails();

        $client_brands_service = new ClientBrands($client_id);
        $client_brands = $client_brands_service->run();

        $brands = $this->getBrandDetails($client_brands);

        if(count($brands) === 0){
            Session::flash('info', ClassMessages::EMPTY_BRAND_FOR_CLIENT);
            return redirect()->back();
        }

        $user_details = new UserDetails($client_details->user_id);

        $industries = new IndustryList();

        $sub_industries = new SubIndustryList();

        //        campaign vs time graph
        $campaign_graph = Utilities::clientGraph($client_campaigns);
        $campaign_payment = $campaign_graph['campaign_payment'];
        $campaign_date = $campaign_graph['campaign_date'];

        return view('broadcaster_module.walk-In.details')->with('clients')
            ->with('client_id', $client_id)
            ->with('user_details', $user_details->getUserDetails())
            ->with('client', $client_details)
            ->with('all_campaigns', $client_campaigns)
            ->with('all_brands', $brands)
            ->with('campaign_payment', $campaign_payment)
            ->with('campaign_date', $campaign_date)
            ->with('total_spent', $client_campaign_service->getClientTotalSpent())
            ->with('industries', $industries->industryList())
            ->with('sub_industries', $sub_industries->getSubIndustryGroupByIndustry());


    }

    public function getBrandDetails($client_brands)
    {
        $brands = [];
        foreach ($client_brands as $client_brand){
            $brand_campaigns_service = new BrandCampaigns($client_brand->id, $client_brand->client_walkins_id,
                $this->broadcaster_id, $this->agency_id);
            $brands[] = [
                'id' => $client_brand->id,
                'client_id' => $client_brand->client_walkins_id,
                'brand' => $client_brand->name,
                'date' => $client_brand->created_at,
                'count_brand' => count($client_brands),
                'campaigns' => $brand_campaigns_service->countAllBrandCampaigns(),
                'image_url' => $client_brand->image_url,
                'last_campaign' => $brand_campaigns_service->countAllBrandCampaigns() != 0 ? $brand_campaigns_service->getBrandLastCampaign()->name : 'none',
                'total' => number_format($brand_campaigns_service->getBrandTotalSpent(),2),
                'industry_id' => $client_brand->industry_code,
                'sub_industry_id' => $client_brand->sub_industry_code,
            ];
        }
        return $brands;
    }


}
