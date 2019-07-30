<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\WalkinStoreRequest;
use Vanguard\Http\Requests\WalkinUpdateRequest;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Models\SubSector;
use Vanguard\Services\Brands\BrandDetails;
use Vanguard\Services\Brands\ClientBrand;
use Vanguard\Services\Brands\CreateBrand;
use Vanguard\Services\Brands\CreateBrandClient;
use Vanguard\Services\Client\ClientDetails;
use Vanguard\Services\User\CreateUser;
use Vanguard\Services\User\UpdateUser;
use Vanguard\Services\Walkin\CreateWalkIns;
use Session;
use Vanguard\Services\Walkin\UpdateWalkIns;

class WalkinsController extends Controller
{
    use CompanyIdTrait;
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getSubIndustry()
    {
        $industry = request()->industry;
        $sub_industry = SubSector::where('sector_id', $industry)->orderBy('name', 'asc')->get();
        if($sub_industry){
            return $sub_industry;
        }else{
            return response()->json(['error' => 'error']);
        }
    }

    public function store(WalkinStoreRequest $request)
    {
        $company_id = $this->companyId();
        $brand_slug = str_slug($request->brand_name);
        $client_brands = new ClientBrand($brand_slug, $company_id);
        if($client_brands->checkForBrandExistence() == 'brand_exist'){
            return [
                'status'=>"error",
                'message'=> ClassMessages::BRAND_ALREADY_EXIST
            ];
        }
        try{
            \DB::transaction(function () use($request, $brand_slug, $company_id) {
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

                    $store_brand_client_service = new CreateBrandClient($this->broadcaster_id,$store_brand->id,$company_id,$store_walkin->id);
                    $store_brand_client_service->storeClientBrand();
                }else{
                    $store_brand_client_service = new CreateBrandClient($this->broadcaster_id,$brand_details->id,$company_id,$store_walkin->id);
                    $store_brand_client_service->storeClientBrand();
                }
            });
            return [
                'status'=>"success",
                'message'=> "Walk-In created successfully, please go to campaign create campaign to select your walk-In"
            ];
        }catch (\Exception $exception){
            return [
                'status'=>"error",
                'message'=> ClassMessages::WALKIN_ERROR
            ];
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
