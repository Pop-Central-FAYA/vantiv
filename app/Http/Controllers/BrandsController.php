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
        $industries = Utilities::switch_db('api')->select("SELECT * FROM sectors");
        $sub_inds = Utilities::switch_db('api')->select("SELECT sub.id, sub.sector_id, sub.name, sub.sub_sector_code from subSectors as sub, sectors as s where sub.sector_id = s.sector_code");
        $brs = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$broadcaster_id' AND status = 0 ORDER BY time_created desc");
        $brands = [];

        foreach ($brs as $br){
            $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE brand = '$br->id'");
            $last_count_campaign = count($campaigns) - 1;
            $pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where brand = '$br->id')");
            $brands[] = [
                'id' => $br->id,
                'brand' => $br->name,
                'date' => $br->time_created,
                'count_brand' => count($brs),
                'campaigns' => count($campaigns),
                'image_url' => $br->image_url,
                'last_campaign' => $campaigns ? $campaigns[$last_count_campaign]->name : 'none',
                'total' => number_format($pay[0]->total,2),
                'industry_id' => $br->industry_id,
                'sub_industry_id' => $br->sub_industry_id,
                'client_id' => $br->walkin_id
            ];
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($brands);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('all-brands');
        return view('broadcaster_module.brands.index')->with('all_brands', $entries)->with('industries', $industries)->with('sub_industries', $sub_inds);

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
        $broadcaster = Session::get('broadcaster_id');
        $broadcaster_user = Session::get('broadcaster_user_id');
        $image_url = '';
        $this->validate($request, [
            'brand_name' => 'required|regex:/^[a-zA-Z- ]+$/',
            'brand_logo' => 'required|image|mimes:png,jpeg,jpg',
            'industry' => 'required',
            'sub_industry' => 'required'
        ]);

        $brand = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
        if(count($ckeck_brand) > 0) {
            return redirect()->back()->with('error', 'Brands already exists');
        }else{

            if($request->hasFile('brand_logo')){
                $image = $request->brand_logo;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_url = encrypt($clouder['url']);
            }

            if(Session::get('broadcaster_id')){
                $insert = Utilities::switch_db('api')->insert("INSERT into brands (id, `name`, image_url, walkin_id, broadcaster_agency, industry_id, sub_industry_id) VALUES ('$unique', '$brand', '$image_url', '$request->walkin_id', '$broadcaster', '$request->industry', '$request->sub_industry')");
            }else{
                $broadcaster = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
                $broadcaster_id = $broadcaster[0]->broadcaster_id;
                $insert = Utilities::switch_db('api')->insert("INSERT into brands (id, `name`, image_url, walkin_id, broadcaster_agency, industry_id, sub_industry_id) VALUES ('$unique', '$brand', '$image_url', '$request->walkin_id', '$broadcaster_id', '$request->industry', '$request->sub_industry')");
            }

            if ($insert) {
                Session::flash('success', 'Brands created successfully');
                return redirect()->route('brand.all');
            } else {
                Session::flash('error', 'There was a problem creating this brand');
                return redirect()->back();
            }
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'brand_name' => 'required|regex:/^[a-zA-Z- ]+$/',
        ]);

        $brand = Utilities::formatString($request->brand_name);
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = 'Brand '.$brand .' Updated by '.Session::get('broadcaster_id');
        $ip = request()->ip();
        $brands = Utilities::switch_db('api')->select("SELECT * from brands WHERE id = '$id'");
        if($brands[0]->name != $brand){
            $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
            if(count($ckeck_brand) > 0) {
                Session::flash('error', 'Brands already exists');
                return redirect()->back();
            }else{
                if($request->has('brand_logo')){
                    $image = $request->brand_logo;
                    $filename = realpath($image);
                    Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                    $clouder = Cloudder::getResult();
                    $image_path = encrypt($clouder['url']);
                    $update_brand = Utilities::switch_db('api')->update("UPDATE brands SET image_url = '$image_path' WHERE id = '$id'");

                }

                $update_brand = Utilities::switch_db('api')->update("UPDATE brands SET name = '$brand', sub_industry_id = '$request->sub_industry' WHERE id = '$id'");

                if($update_brand == true) {
                    $user_activity = Api::saveActivity(Session::get('broadcaster_id'), $description, $ip, $user_agent);
                    Session::flash('success', 'Brands updated successfully');
                    return redirect()->back();
                }else{
                    Session::flash('error', 'There was a problem updating this brand');
                    return redirect()->back();
                }
            }
        }else{
            if($request->hasFile('brand_logo')){
                $image = $request->brand_logo;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_path = encrypt($clouder['url']);
                $update_brand = Utilities::switch_db('api')->update("UPDATE brands SET image_url = '$image_path' WHERE id = '$id'");

            }

            $update_brand = Utilities::switch_db('api')->update("UPDATE brands SET name = '$brand', sub_industry_id = '$request->sub_industry' WHERE id = '$id'");

            if($update_brand == true) {
                $user_activity = Api::saveActivity(Session::get('broadcaster_id'), $description, $ip, $user_agent);
                Session::flash('success', 'Brands updated successfully');
                return redirect()->back();
            }else{
                Session::flash('error', 'There was a problem updating this brand');
                return redirect()->back();
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $brand = Utilities::switch_db('api')->update("UPDATE brands set status = 1 WHERE id = '$id'");
        if($brand)
        {
            Session::flash('success', 'Brands Deleted Successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'There was a problem deleting this brand');
            return redirect()->back();
        }
    }

    public function search()
    {
        $broadcaster = Session::get('broadcaster_id');
        $broadcaster_user = Session::get('broadcaster_user_id');
        if(!Session::get('broadcaster_user_id')){
            $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
            $broadcaster = $broadcaster_id[0]->broadcaster_id;
        }
        $request = request();
        $result = $request->result;
        $this->validate($request, [
            'result' => 'required',
        ]);
        $brand = Utilities::switch_db('api')->select("SELECT * from brands where `name` LIKE '%{$result}%' AND broadcaster_agency = '$broadcaster' AND status = 0 ORDER BY time_created desc");
        if($brand){
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $col = new Collection($brand);
            $perPage = 10;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
            $entries->setPath('brands');
            return view('brands.result.index')->with('brands', $entries)->with('result', $result);
        }else{
            Session::flash('error', 'No result found for '.$result.'');
            return back();
        }
    }

    public function getBrandsWithClients($id)
    {
        $brands = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id = '$id'");
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
        $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.name, b.name as brand_name, p.total, c_d.product, c_d.time_created, c_d.start_date, c_d.stop_date, c_d.adslots, c.campaign_reference FROM campaignDetails as c_d, campaigns as c, brands as b, payments as p where c_d.brand = '$id' and b.id = '$id' and c_d.broadcaster = '$broadcaster_id' and c_d.campaign_id = c.id and c_d.campaign_id = p.campaign_id");

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
}
