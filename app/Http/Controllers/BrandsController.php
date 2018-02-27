<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Carbon\Carbon;
use Session;

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
        $db = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$broadcaster' ORDER BY time_created desc");
        return view('brands.index')->with('brand', $db);
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
        foreach ($walkins as $walk)
        {
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
            'image_url' => 'required'
        ]);

        $brand = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns WHERE user_id = '$request->clients'");
        $id = $walkin_id[0]->id;
                return redirect()->route('brand.all')->with('success', 'Brands created successfully');
            } else {
                return redirect()->back()->with('error', 'There was a problem creating this brand');
            }
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'brand_name' => 'required|regex:/^[a-zA-Z- ]+$/',
            'image_url' => 'required'
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
        $brand = Utilities::switch_db('api')->select("DELETE FROM brands WHERE id = '$id'");
        if(!$brand)
        {
            return redirect()->back()->with('success', 'Brands Deleted Successfully');
        }else{
            return redirect()->back()->with('error', 'There was a problem deleting this brand');
        }
    }
}
