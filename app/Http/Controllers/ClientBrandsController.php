<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;
use Carbon\Carbon;
use Session;
use Image;
use JD\Cloudder\Facades\Cloudder;

class ClientBrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Session::get('agency_id') != null) {
            $agrncy_id = Session::get('agency_id');
            $db = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$agrncy_id' AND status = 0 ORDER BY time_created desc");
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $col = new Collection($db);
            $perPage = 10;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
            $entries->setPath('all-brands');
            return view('agency.campaigns.brands.index')->with('brand', $entries);
        } else {
            $advertiser_id = Session::get('advertiser_id');
            $db = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$advertiser_id' AND status = 0 ORDER BY time_created desc");
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $col = new Collection($db);
            $perPage = 10;
            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
            $entries->setPath('all-brands');
            return view('advertisers.campaigns.brands.index')->with('brand', $entries);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $client = [];
        $agency_id = \Session::get('agency_id');

        if ($agency_id) {
            $walkins = Utilities::switch_db('api')->select("SELECT user_id from walkIns where agency_id = '$agency_id'");
            foreach ($walkins as $walk) {
                $user_id = $walk->user_id;
                $cli = \DB::select("SELECT * from users WHERE id = '$user_id'");
                $client[] = $cli;
            }
            
            return view('agency.campaigns.brands.create')->with('clients', $client);
        } else {
            return view('advertisers.campaigns.brands.create');
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'brand_name' => 'required|regex:/^[a-zA-Z- ]+$/',
            'brand_logo' => 'required|image|mimes:png,jpeg,jpg'
        ]);

        $brand = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = Session::get('agency_id') ? 'Brand '.$request->brand_name .' Created by '.Session::get('agency_id') : 'Brand '.$request->brand_name .' Created by '.Session::get('advertiser_id');
        $ip = request()->ip();
        if(Session::get('agency_id') != null){
            $agency_id = Session::get('agency_id');
            $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns WHERE user_id = '$request->clients'");
            $id = $walkin_id[0]->id;
            $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
            if(count($ckeck_brand) > 0) {
                return redirect()->back()->with('error', 'Brands already exists');
            }else{

                /*handling uploading the logo*/
                $image = $request->brand_logo;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_path = encrypt($clouder['url']);

                $insert = Utilities::switch_db('api')->select("INSERT into brands (id, `name`, walkin_id, broadcaster_agency, image_url) VALUES ('$unique','$brand','$id', '$agency_id', '$image_path')");
                $user_activity = Api::saveActivity($agency_id, $description, $ip, $user_agent);
                if(!$insert) {
                    return redirect()->route('agency.brand.all')->with('success', 'Brands created successfully');
                }else{
                    return redirect()->back()->with('error', 'There was a problem creating this brand');
                }
            }
        }else{
            $advertiser_id = Session::get('advertiser_id');
            $user = Utilities::switch_db('api')->select("SELECT * from users WHERE id = (SELECT user_id from advertisers where id = '$advertiser_id')");
            $user_id = $user[0]->id;
            $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
            if(count($ckeck_brand) > 0) {
                return redirect()->back()->with('error', 'Brands already exists');
            }else{
                /*handling uploading the logo*/
                $image = $request->brand_logo;
                $filename = realpath($image);
                Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
                $clouder = Cloudder::getResult();
                $image_path = encrypt($clouder['url']);

                $insert = Utilities::switch_db('api')->select("INSERT into brands (id, `name`, walkin_id, broadcaster_agency, image_url) VALUES ('$unique','$brand','$user_id', '$advertiser_id', '$image_path')");
                $user_activity = Api::saveActivity($advertiser_id, $description, $ip, $user_agent);
                if(!$insert) {
                    return redirect()->route('agency.brand.all')->with('success', 'Brands created successfully');
                }else{
                    return redirect()->back()->with('error', 'There was a problem creating this brand');
                }
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
        $description = Session::get('agency_id') ? 'Brand '.$brand .' Updated by '.Session::get('agency_id') : 'Brand '.$brand .' Updated by '.Session::get('advertiser_id');
        $ip = request()->ip();
        $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
        if(count($ckeck_brand) > 0) {
            return redirect()->back()->with('error', 'Brands already exists');
        }else{
            $update_brand = Utilities::switch_db('api')->select("UPDATE brands SET name = '$brand' WHERE id = '$id'");
            if(!$update_brand) {
                if(Session::get('agency_id')){
                    $user_activity = Api::saveActivity(Session::get('agency_id'), $description, $ip, $user_agent);
                }else{
                    $user_activity = Api::saveActivity(Session::get('advertiser_id'), $description, $ip, $user_agent);
                }
                return redirect()->route('agency.brand.all')->with('success', 'Brands updated successfully');
            }else{
                return redirect()->back()->with('error', 'There was a problem updating this brand');
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
        $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$id'");
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $description = Session::get('agency_id') ? 'Brand '.$brand_name[0]->name.' Deleted by '.Session::get('agency_id') : 'Brand '.$brand_name[0]->name.' Deleted by '.Session::get('advertiser_id');
        $ip = request()->ip();
        if(Session::get('agency_id')){
            $user_activity = Api::saveActivity(Session::get('agency_id'), $description, $ip, $user_agent);
        }else{
            $user_activity = Api::saveActivity(Session::get('advertiser_id'), $description, $ip, $user_agent);
        }
        $brand = Utilities::switch_db('api')->update("UPDATE brands set status = 1 WHERE id = '$id'");
        if($brand)
        {
            return redirect()->back()->with('success', 'Brands Deleted Successfully');
        }else{
            return redirect()->back()->with('error', 'There was a problem deleting this brand');
        }
    }

    public function search()
    {
        $agency_id = Session::get('agency_id');
        $request = request();
        $result = $request->result;
        $this->validate($request, [
            'result' => 'required',
        ]);

        if($agency_id){
            $db = Utilities::switch_db('api')->select("SELECT * from brands where `name` LIKE '%{$result}%' AND broadcaster_agency = '$agency_id' AND status = 0 ORDER BY time_created desc");
            if($db){
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $col = new Collection($db);
                $perPage = 10;
                $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
                $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
                $entries->setPath('all-brands');
                return view('agency.campaigns.brands.result.index')->with('brand', $entries)->with('result', $result);
            }else{
                return back()->withErrors('No result found for '.$result.'');
            }

        }else{
            $advertiser_id = Session::get('advertiser_id');
            $db = Utilities::switch_db('api')->select("SELECT * from brands where `name` LIKE '%{$result}%' AND broadcaster_agency = '$advertiser_id' AND status = 0 ORDER BY time_created desc");
            if($db){
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $col = new Collection($db);
                $perPage = 10;
                $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
                $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
                $entries->setPath('all-brands');
                return view('advertisers.campaigns.brands.result.index')->with('brand', $entries)->with('result', $result);
            }else{
                return back()->withErrors('No result found for '.$result.'');
            }

        }

    }
}
