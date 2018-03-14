<?php

namespace Vanguard\Http\Controllers;

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
        $broadcaster = Session::get('broadcaster_id');
        $client = [];
        $walkins = Utilities::switch_db('api')->select("SELECT user_id from walkIns where broadcaster_id = '$broadcaster'");
        foreach ($walkins as $walk) {
            $user_id = $walk->user_id;
            $cli = Utilities::switch_db('api')->select("SELECT * from users WHERE id = '$user_id'");
            $client[] = $cli;
        }

        return view('brands.create')->with('client', $client);
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

        $this->validate($request, [
            'brand_name' => 'required|regex:/^[a-zA-Z- ]+$/',
            'image_url' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        $image = $request->image_url;
        $filename = realpath($image);
        Cloudder::upload($filename, Cloudder::getPublicId(), ['height' => 200, 'width' => 200]);
        $clouder = Cloudder::getResult();
        $image_url = encrypt($clouder['url']);


        $brand = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns WHERE user_id = '$request->clients'");
        $id = $walkin_id[0]->id;
        $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
        if(count($ckeck_brand) > 0) {
            return redirect()->back()->with('error', 'Brands already exists');
        }else{
            $insert = Utilities::switch_db('api')->select("INSERT into brands (id, `name`, image_url, walkin_id, broadcaster_agency) VALUES ('$unique', '$brand', '$image_url', '$id', '$broadcaster')");
            if(!$insert) {
                return redirect()->route('brand.all')->with('success', 'Brands created successfully');
            }else{
                return redirect()->back()->with('error', 'There was a problem creating this brand');
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
        if(count($ckeck_brand) > 0) {
            return redirect()->back()->with('error', 'Brands already exists');
        }else{
            $update_brand = Utilities::switch_db('api')->select("UPDATE brands SET name = '$brand' WHERE id = '$id'");
            if(!$update_brand) {
                return redirect()->back()->with('success', 'Brands updated successfully');
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
        $broadcaster = Session::get('broadcaster_id');
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
            return back()->withErrors('No result found for '.$result.'');
        }
    }
}
