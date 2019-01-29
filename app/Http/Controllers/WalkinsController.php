<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Http\Requests\StoreWalkins;
use Vanguard\Http\Requests\WalkinStoreRequest;
use Vanguard\Http\Requests\WalkinUpdateRequest;
// use Vanguard\Libraries\Amazon;
use Vanguard\Libraries\AmazonS3;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Models\Brand;
use Vanguard\Services\Brands\BrandDetails;
use Vanguard\Services\Brands\ClientBrand;
use Vanguard\Services\Brands\CreateBrand;
use Vanguard\Services\Brands\CreateBrandClient;
use Vanguard\Services\User\CreateUser;
use Vanguard\Services\Walkin\CreateWalkIns;
use Yajra\DataTables\DataTables;
use Vanguard\Libraries\Utilities;
use Session;
use Vanguard\Libraries\Api;
use Illuminate\Support\Facades\DB;

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
        $broadcaster_id = Session::get('broadcaster_id');

        $clients = Utilities::switch_db('api')->select("SELECT w.user_id, w.id, u.id as user_det_id, u.firstname, u.lastname, u.phone_number,
                                                                w.location, w.company_logo, w.company_name, w.time_created, u.email, w.image_url from walkIns as w
                                                                INNER JOIN users as u ON u.id = w.user_id where w.broadcaster_id = '$broadcaster_id'");

        $client_data = $this->getClientDetails($clients, $broadcaster_id);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($client_data);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('list');

        $industries = Utilities::switch_db('api')->select("
            SELECT * FROM sectors
            ORDER BY `name` ASC
        ");

        return view('broadcaster_module.walk-In.index')->with('clients', $entries)->with('industries', $industries);
    }

    public function getClientDetails($clients, $broadcaster_id)
    {
        $client_data = [];

        foreach ($clients as $client) {

            $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where user_id = '$client->user_id' and broadcaster = '$broadcaster_id'");
            $last_count_campaign = count($campaigns) - 1;

            $active_campaigns = [];

            $inactive_campaigns = [];

            $brs = Utilities::getBrandsForWalkins($client->id);

            $today = date("Y-m-d");

            foreach ($campaigns as $campaign){
                if($campaign->stop_date > $today){
                    $active_campaigns[] = $campaign;
                }else{
                    $inactive_campaigns[] = $campaign;
                }
            }

            $payments = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments WHERE campaign_id IN
                                                              (SELECT campaign_id from campaignDetails WHERE user_id = '$client->user_id' and broadcaster = '$broadcaster_id')");

            $client_data[] = [
                'client_id' => $client->id,
                'user_id' => $client->user_id,
                'agency_client_id' => $client->user_det_id,
                'image_url' => $client->image_url,
                'num_campaign' => $campaigns ? count($campaigns) : 0,
                'total' => $payments[0]->total,
                'name' =>  $client->lastname . ' ' . $client->firstname,
                'email' => $client->email,
                'phone_number' => $client->phone_number,
                'first_name' => $client->firstname,
                'last_name' => $client->lastname,
                'created_at' => $client->time_created,
                'last_camp' => $campaigns ? $campaigns[$last_count_campaign]->time_created : 0,
                'active_campaign' => count($active_campaigns),
                'inactive_campaign' => count($inactive_campaigns),
                'count_brands' => count($brs),
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
                    $store_brand_client = $store_brand_client_service->storeClientBrand();
                }else{
                    $store_brand_client_service = new CreateBrandClient($this->broadcaster_id,$brand_details->id,$client_id,$store_walkin->id);
                    $store_brand_client = $store_brand_client_service->storeClientBrand();
                }
            });
        }catch (\Exception $exception){
            dd($exception);
        }

        if($this->broadcaster_id){
            return redirect()->route('walkins.all');
        }else{
            return redirect()->route('clients.list');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWalKins(WalkinUpdateRequest $request, $client_id)
    {
        $result = Utilities::updateClients($request, $client_id);

        if($result === "success"){
            Session::flash('success', 'Client profile updated successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'An error occurred while submitting your request');
            return redirect()->back();
        }
    }

    public function getDetails($client_id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");

        $user_id = $client[0]->user_id;

        $all_campaigns = Utilities::getClientCampaignData($user_id, $broadcaster_id);

        $all_brands = Utilities::getClientsBrands($client_id, $broadcaster_id);

        if(count($all_brands) === 0){
            Session::flash('info', 'You don`t have a brand on this client');
            return redirect()->back();
        }

        $total = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where user_id = '$user_id' and broadcaster = '$broadcaster_id') ");

        $campaigns = Utilities::switch_db('api')->select("SELECT c.campaign_id, c.adslots, c.time_created, c.product, p.total, c.time_created from campaignDetails as c
                                                              INNER JOIN payments as p ON p.campaign_id = c.campaign_id where c.user_id = '$user_id' and c.broadcaster = '$broadcaster_id'");

        $user_camp = Utilities::clientscampaigns($campaigns);

        $user_details = Utilities::switch_db('api')->select("SELECT * FROM users where id = '$user_id'");

//        campaign vs time graph
        $campaign_graph = Utilities::clientGraph($campaigns);
        $campaign_payment = $campaign_graph['campaign_payment'];
        $campaign_date = $campaign_graph['campaign_date'];

        $industries = Utilities::switch_db('api')->select("
            SELECT * FROM sectors
            ORDER BY `name` ASC
        ");

        $sub_inds = Utilities::switch_db('api')->select("SELECT sub.id, sub.sector_id, sub.name, sub.sub_sector_code from subSectors as sub, sectors as s where sub.sector_id = s.sector_code");

        return view('broadcaster_module.walk-In.details')->with('clients')
            ->with('client_id', $client_id)
            ->with('client', $client)
            ->with('user_details', $user_details)
            ->with('campaign', $user_camp)
            ->with('all_campaigns', $all_campaigns)
            ->with('all_brands', $all_brands)
            ->with('total', $total)
            ->with('campaign_payment', $campaign_payment)
            ->with('campaign_date', $campaign_date)
            ->with('total_spent', $total)
            ->with('industries', $industries)
            ->with('sub_industries', $sub_inds);
    }

}
