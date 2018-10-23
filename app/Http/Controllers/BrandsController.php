<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Image;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use JD\Cloudder\Facades\Cloudder;
use Vanguard\Models\Brand;
use Vanguard\Models\BrandClient;


class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $agency_id = Session::get('agency_id');
        if($broadcaster_id){
            $broadcaster_agency_id = $broadcaster_id;
        }else{
            $broadcaster_agency_id = $agency_id;
        }
        $industries = Utilities::switch_db('api')->select("SELECT * FROM sectors");
        $sub_inds = Utilities::switch_db('api')->select("SELECT sub.id, sub.sector_id, sub.name, sub.sub_sector_code from subSectors as sub, sectors as s where sub.sector_id = s.sector_code");
        $all_brands = Utilities::getBrands($broadcaster_agency_id);
        $brands = [];

        foreach ($all_brands as $all_brand){
            if($broadcaster_id){
                $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE brand = '$all_brand->id' AND walkins_id = '$all_brand->client_walkins_id'");
            }else{
                $campaigns = Utilities::switch_db('api')->select("SELECT *  from campaignDetails WHERE brand = '$all_brand->id' AND walkins_id = '$all_brand->client_walkins_id' GROUP BY campaign_id");
            }
            $last_count_campaign = count($campaigns) - 1;
            if($broadcaster_id){
                $pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where 
                                                            brand = '$all_brand->id' and walkins_id = '$all_brand->client_walkins_id')");
            }else{
                $pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where 
                                                              brand = '$all_brand->id' GROUP BY campaign_id)");
            }

            $brands[] = [
                'id' => $all_brand->id,
                'brand' => $all_brand->name,
                'date' => $all_brand->created_at,
                'count_brand' => count($all_brands),
                'campaigns' => count($campaigns),
                'image_url' => Utilities::returnImageWhenNotEncrypted($all_brand->image_url),
                'last_campaign' => $campaigns ? $campaigns[$last_count_campaign]->name : 'none',
                'total' => number_format($pay[0]->total,2),
                'industry_id' => $all_brand->industry_code,
                'sub_industry_id' => $all_brand->sub_industry_code,
                'client_id' => $all_brand->client_walkins_id
            ];
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($brands);
        $perPage = 5;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('brands');
        if($broadcaster_id){
            return view('broadcaster_module.brands.index')->with('all_brands', $entries)->with('industries', $industries)->with('sub_industries', $sub_inds);
        }else{
            return view('agency.campaigns.brands.index')->with('all_brands', $entries)->with('industries', $industries)->with('sub_industries', $sub_inds);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Session::get('broadcaster_id')){
            $broadcaster = Session::get('broadcaster_id');
        }else{
            $broadcaster = Session::get('broadcaster_user_id');
        }

        $client = [];
        $industries = Utilities::switch_db('api')->select("SELECT * from sectors");
        if(Session::get('broadcaster_id')){
            $walkins = Utilities::switch_db('api')->select("SELECT user_id from walkIns where broadcaster_id = '$broadcaster'");
        }else{
            $walkins = Utilities::switch_db('api')->select("SELECT user_id from walkIns where agency_id = '$broadcaster'");
        }

        foreach ($walkins as $walk) {
            $user_id = $walk->user_id;
            $cli = Utilities::switch_db('api')->select("SELECT * from users WHERE id = '$user_id'");
            $client[] = $cli;
        }

//        $industires = Utilities::switch_db('api')->select("SELECT * from ")

        return view('brands.create')->with('clients', $client)->with('industries', $industries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $broadcaster_id = Session::get('broadcaster_id');

        $agency_id = Session::get('agency_id');

        if($broadcaster_id){
            $broadcaster_agency_id = $broadcaster_id;
        }else{
            $broadcaster_agency_id = $agency_id;
        }

        $api_db = Utilities::switch_db('api');

        $api_db->beginTransaction();

        $brand_slug = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $check_brand = $api_db->select("SELECT b.* from brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id 
                                                                WHERE b.slug = '$brand_slug' AND client_id = '$broadcaster_agency_id'");
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
                Utilities::storeBrandClient($unique, $broadcaster_agency_id, $request->walkin_id);
            }catch (\Exception $e){
                $api_db->rollback();
                Session::flash('error', 'There was a problem creating this walk-In');
                return redirect()->back();
            }

        }else{
            try {
                Utilities::storeBrandClient($checkIfBrandExists->id, $broadcaster_agency_id, $request->walkin_id);

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

        $broadcaster_id = Session::get('broadcaster_id');

        $agency_id = Session::get('agency_id');

        if($broadcaster_id){
            $broadcaster_agency_id = $broadcaster_id;
        }else{
            $broadcaster_agency_id = $agency_id;
        }

        $api_db = Utilities::switch_db('api');

        $api_db->beginTransaction();

        $brand_slug = Utilities::formatString($request->brand_name);
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = 'Brand '.$request->brand_name .' Updated by '.Session::get('broadcaster_id');
        $ip = request()->ip();
        $brands = Brand::where('id', $id)->first();

        if($brands->slug != $brand_slug){
            $check_brand = $api_db->select("SELECT b.* from brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id 
                                                                WHERE b.slug = '$brand_slug' AND client_id = '$broadcaster_agency_id'");
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

                $user_activity = Api::saveActivity($broadcaster_agency_id, $description, $ip, $user_agent);

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

            $user_activity = Api::saveActivity($broadcaster_agency_id, $description, $ip, $user_agent);

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
        $brands = Utilities::getBrandsForWalkins($id);
        return response()->json(['brands' => $brands]);
    }

    public function getBrandDetails($id, $client_id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $campaigns = [];
        //get client details
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");
        $user_id = $client[0]->user_id;
        $user_details = Utilities::switch_db('api')->select("SELECT * FROM users where id = '$user_id'");

        $this_brand = Utilities::switch_db('api')->select("SELECT * FROM brands where id = '$id'");
        $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.name, b.name as brand_name, p.total, c_d.product, c_d.time_created, c_d.start_date, 
                                                                c_d.stop_date, c_d.adslots, c.campaign_reference FROM campaignDetails as c_d
                                                                INNER JOIN campaigns as c ON c.id = c_d.campaign_id 
                                                                INNER JOIN brands as b ON c_d.brand = b.id
                                                                INNER JOIN payments as p ON p.campaign_id = c_d.campaign_id 
                                                                where c_d.brand = '$id' and b.id = '$id' and c_d.broadcaster = '$broadcaster_id' and c_d.walkins_id = '$client_id'");

        foreach ($all_campaigns as $cam)
        {
            $mpo = Utilities::switch_db('api')->select("SELECT * FROM mpoDetails where mpo_id = (SELECT id from mpos where campaign_id = '$cam->campaign_id') LIMIT 1");
            $today = date("Y-m-d");
            if(strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)){
                $status = 'Expired';
            }elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)){
                $status = 'Active';
            }else{
                $status = 'pending';
            }

            $campaigns[] = [
                'id' => $cam->campaign_reference,
                'camp_id' => $cam->campaign_id,
                'name' => $cam->name,
                'brand' => $cam->brand_name,
                'product' => $cam->product,
                'date_created' => date('Y/m/d',strtotime($cam->time_created)),
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'adslots' => $cam->adslots,
                'budget' => number_format($cam->total, 2),
                'compliance' => '0%',
                'status' => $status,
                'mpo_status' => $mpo[0]->is_mpo_accepted
            ];
        }

        return view('broadcaster_module.brands.details', compact('this_brand', 'campaigns', 'user_details', 'client_id', 'client'));
    }

    public function checkBrandExistsWithSameInformation(Request $request)
    {
        $brand_slug = Utilities::formatString($request->brand_name);
        $checkIfBrandExists = Brand::where('slug', $brand_slug)->first();
        if($checkIfBrandExists){
            return 'already_exists';
        }
    }
}
