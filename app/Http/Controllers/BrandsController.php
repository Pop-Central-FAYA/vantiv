<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Session;
use Illuminate\Http\Request;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use Vanguard\Models\Brand;
use Vanguard\Services\Client\ClientBrand;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Services\Industry\SubIndustryList;
use Vanguard\Services\Industry\IndustryList;
use Vanguard\Services\Brands\CompanyBrands;
use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{
    use CompanyIdTrait;

    public $brand_view;
    public $client_id;
    public $brand_id;

    public function getLayout()
    {
        $this->brand_view = 'agency.campaigns.brands.index';
    }

    public function __construct()
    {
        $this->getLayout();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $industries = (new IndustryList())->industryList();
        $sub_inds = (new SubIndustryList())->getSubIndustryGroupByIndustry();
        $brands = $this->getBrandData();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($brands);
        $perPage = 5;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('brands');
        return view($this->brand_view)->with('all_brands', $entries)
                                                    ->with('industries', $industries)
                                                    ->with('sub_industries', $sub_inds);
    
    }

    public function getBrandData() 
    {
        $company_id = $this->companyId();
        $all_brands = (new CompanyBrands($company_id))->getBrandCreatedByCompany();
        $brands = [];
        foreach ($all_brands as $all_brand){
            $this->brand_id = $all_brand->id;
            $this->client_id = $all_brand->client_walkins_id;
            $campaigns = $this->getBrandCampaigns();
            $last_count_campaign = count($campaigns) - 1;
            $pay = $this->getBrandTotalSpent();
            $brands[] = [
                'id' => $all_brand->id,
                'brand' => $all_brand->name,
                'date' => $all_brand->created_at,
                'count_brand' => count($all_brands),
                'campaigns' => count($campaigns),
                'image_url' => $all_brand->image_url,
                'last_campaign' => $campaigns ? $campaigns[$last_count_campaign]->name : 'none',
                'total' => number_format($pay[0]->total,2),
                'industry_id' => $all_brand->industry_code,
                'sub_industry_id' => $all_brand->sub_industry_code,
                'client_id' => $all_brand->client_walkins_id
            ];
        }
        return $brands;
    }

    public function getBrandCampaigns()
    {
        return DB::select("SELECT * FROM campaignDetails 
                                WHERE brand = '$this->brand_id' 
                                AND walkins_id = '$this->client_id'
                                ");
    }

    public function getBrandTotalSpent()
    {
        return DB::select("SELECT SUM(total) AS total 
                            FROM payments 
                            WHERE campaign_id 
                            IN (SELECT campaign_id 
                                    FROM campaignDetails 
                                    WHERE brand = '$this->brand_id' 
                                    GROUP BY campaign_id
                                )
                            ");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $company_id = $this->companyId();
        $api_db = Utilities::switch_db('api');

        $api_db->beginTransaction();

        $brand_slug = str_slug($request->brand_name);
        $unique = uniqid();
        $check_brand = $api_db->select("SELECT b.* from brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id 
                                                                WHERE b.slug = '$brand_slug' AND client_id = '$company_id'");
        if(count($check_brand) > 0) {
            Session::flash('error', 'Brands already exists');
            return redirect()->back();
        }

        //check if the brand exists in the brands table and if not create the brand in the brands table and attach the client in the brand_client table.
        $checkIfBrandExists = Brand::where('slug', $brand_slug)->first();
        if(!$checkIfBrandExists){
            $brand = new Brand();
            try {
                Utilities::storeBrands($brand, $request, $unique, $request->image_url, $brand_slug);

            }catch (\Exception $e){
                $api_db->rollback();
                Session::flash('error', 'There was a problem creating this brand');
                return redirect()->back();
            }

            try{
                Utilities::storeBrandClient($unique, $company_id, $request->walkin_id);
            }catch (\Exception $e){
                $api_db->rollback();
                Session::flash('error', 'There was a problem creating this walk-In');
                return redirect()->back();
            }

        }else{
            try {
                Utilities::storeBrandClient($checkIfBrandExists->id, $company_id, $request->walkin_id);

            }catch (\Exception $e){
                $api_db->rollback();
                Session::flash('error', 'There was a problem creating this brand');
                return redirect()->back();
            }
        }

        Session::flash('success', 'Brands created successfully');
        
        $api_db->commit();

        return redirect()->route('brand.all');

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'brand_name' => 'required|regex:/^[a-zA-Z- ]+$/',
        ]);

        $company_id = $this->companyId();

        $api_db = Utilities::switch_db('api');

        $api_db->beginTransaction();

        $brand_slug = str_slug($request->brand_name);
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = 'Brand '.$request->brand_name .' Updated by '.Session::get('broadcaster_id');
        $ip = request()->ip();
        $brands = Brand::where('id', $id)->first();

        if($brands->slug != $brand_slug){
            $check_brand = $api_db->select("SELECT b.* from brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id 
                                                                WHERE b.slug = '$brand_slug' AND client_id = '$company_id'");
            if(count($check_brand) > 0) {
                Session::flash('error', 'Brands already exists');
                return redirect()->back();
            }else{
                try {
                    if($request->image_url){
                        $brands->image_url = $request->image_url;
                        $brands->save();
                    }
                    $brands->name = $request->brand_name;
                    $brands->sub_industry_code = $request->sub_industry;
                    $brands->slug = $brand_slug;
                    $brands->save();

                }catch (\Exception $e){
                    $api_db->rollback();
                    Session::flash('error', 'There was problem updating your brand');
                    return redirect()->back();
                }

                $user_activity = Api::saveActivity($company_id, $description, $ip, $user_agent);

            }
        }else{
            try {
                if($request->image_url){
                    $brands->image_url = $request->image_url;
                    $brands->save();
                }
                $brands->name = $request->brand_name;
                $brands->sub_industry_code = $request->sub_industry;
                $brands->slug = $brand_slug;
                $brands->save();

            }catch (\Exception $e){
                $api_db->rollback();
                Session::flash('error', 'There was problem updating your brand');
                return redirect()->back();
            }

            $user_activity = Api::saveActivity($company_id, $description, $ip, $user_agent);

        }

        $api_db->commit();
        Session::flash('success', 'Brands updated successfully');
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $brand = Brand::where('id', $id)->first();
        $brand->status = 1;
        Session::flash('success', 'Brands Deleted Successfully');
        return redirect()->back();
    }

    public function getBrandsWithClients($id)
    {
        $client_brands = new ClientBrand($id);
        $brands = $client_brands->run();
        return response()->json(['brands' => $brands]);
    }

    public function checkBrandExistsWithSameInformation(Request $request)
    {
        $brand_slug = str_slug($request->brand_name);
        $checkIfBrandExists = Brand::where('slug', $brand_slug)->first();
        if($checkIfBrandExists){
            return 'already_exists';
        }
    }
}
