<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Carbon\Carbon;

class ClientBrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $db = Utilities::switch_db('api')->select("SELECT * from brands");
        return view('agency.campaigns.brands.index')->with('brand', $db);
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
        $walkins = Utilities::switch_db('api')->select("SELECT user_id from walkIns where agency_id = '$agency_id'");
        foreach ($walkins as $walk)
        {
            $user_id = $walk->user_id;
            $cli = \DB::select("SELECT * from users WHERE id = '$user_id'");
            $client[] = $cli;
        }
        return view('agency.campaigns.brands.create')->with('client', $client);
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
        ]);

        $brand = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns WHERE user_id = '$request->clients'");
        $id = $walkin_id[0]->id;
        $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
        if(count($ckeck_brand) > 0) {
            return redirect()->back()->with('error', 'Brands already exists');
        }else{
            $insert = Utilities::switch_db('api')->select("INSERT into brands (id, `name`, walkin_id) VALUES ('$unique','$brand','$id')");
            if(!$insert) {
                return redirect()->back()->with('success', 'Brands created successfully');
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
        $brand = Utilities::switch_db('api')->select("DELETE FROM brands WHERE id = '$id'");
        if(!$brand)
        {
            return redirect()->back()->with('success', 'Brands Deleted Successfully');
        }else{
            return redirect()->back()->with('error', 'There was a problem deleting this brand');
        }
    }
}
