<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Image;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $broadcaster = Session::get('broadcaster_id');
        if(!Session::get('broadcaster_id')){
            $broadcaster_user = Session::get('broadcaster_user_id');
            $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
            $broadcaster = $broadcaster_id[0]->broadcaster_id;
        }
        $db = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$broadcaster' AND status = 0 ORDER BY time_created desc");
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($db);
        $perPage = 10;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath('brands');
        return view('brands.index')->with('brands', $entries);

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
//            'image_url' => 'image|mimes:jpg,jpeg,png',
        ]);

        if($request->hasFile('image_url')){
            $image = $request->image_url;
            $filename = realpath($image);
            Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
            $clouder = Cloudder::getResult();
            $image_url = encrypt($clouder['url']);
        }

        $brand = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns WHERE user_id = '$request->clients'");
        $id = $walkin_id[0]->id;
        $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
        if(count($ckeck_brand) > 0) {
            return redirect()->back()->with('error', 'Brands already exists');
        }else{
            if(Session::get('broadcaster_id')){
                $insert = Utilities::switch_db('api')->select("INSERT into brands (id, `name`, image_url, walkin_id, broadcaster_agency, industry_id, sub_industry_id) VALUES ('$unique', '$brand', '$image_url', '$id', '$broadcaster', '$request->industry', '$request->sub_industry')");
            }else{
                $broadcaster = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
                $broadcaster_id = $broadcaster[0]->broadcaster_id;
                $insert = Utilities::switch_db('api')->select("INSERT into brands (id, `name`, image_url, walkin_id, broadcaster_agency, industry_id, sub_industry_id) VALUES ('$unique', '$brand', '$image_url', '$id', '$broadcaster_id', '$request->industry', '$request->sub_industry')");
            }

            if (!$insert) {
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
        $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
        if (count($ckeck_brand) > 0) {
            return redirect()->back()->with('error', 'Brands already exists');
        } else {
            $update_brand = Utilities::switch_db('api')->select("UPDATE brands SET name = '$brand' WHERE id = '$id'");
            if(!$update_brand) {
                Session::flash('success', 'Brand Updated Successfully');
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
}
