<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Carbon\Carbon;
use Session;

class ClientBrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Session::get('agency_id') != null){
            $agrncy_id = Session::get('agency_id');
            $db = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$agrncy_id' ORDER BY time_created desc");
            return view('agency.campaigns.brands.index')->with('brand', $db);
        }else{
            $advertiser_id = Session::get('advertiser_id');
            $db = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$advertiser_id' ORDER BY time_created desc");
            return view('advertisers.campaigns.brands.index')->with('brand', $db);
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
        if($agency_id){
            $walkins = Utilities::switch_db('api')->select("SELECT user_id from walkIns where agency_id = '$agency_id'");
            foreach ($walkins as $walk)
            {
                $user_id = $walk->user_id;
                $cli = \DB::select("SELECT * from users WHERE id = '$user_id'");
                $client[] = $cli;
            }
            return view('agency.campaigns.brands.create')->with('client', $client);
        }else{
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
        ]);

        $brand = Utilities::formatString($request->brand_name);
        $unique = uniqid();
        if(Session::get('agency_id') != null){
            $agency_id = Session::get('agency_id');
            $walkin_id = Utilities::switch_db('api')->select("SELECT id from walkIns WHERE user_id = '$request->clients'");
            $id = $walkin_id[0]->id;
            $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
            if(count($ckeck_brand) > 0) {
                return redirect()->back()->with('error', 'Brands already exists');
            }else{
                $insert = Utilities::switch_db('api')->select("INSERT into brands (id, `name`, walkin_id, broadcaster_agency) VALUES ('$unique','$brand','$id', '$agency_id')");
                if(!$insert) {
                    return redirect()->back()->with('success', 'Brands created successfully');
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
                $insert = Utilities::switch_db('api')->select("INSERT into brands (id, `name`, walkin_id, broadcaster_agency) VALUES ('$unique','$brand','$user_id', '$advertiser_id')");
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
        $ckeck_brand = Utilities::switch_db('api')->select("SELECT name from brands WHERE `name` = '$brand'");
        if(count($ckeck_brand) > 0) {
            return redirect()->back()->with('error', 'Brands already exists');
        }else{
            $update_brand = Utilities::switch_db('api')->select("UPDATE brands SET name = '$brand' WHERE id = '$id'");
            if(!$update_brand) {
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
        $brand = Utilities::switch_db('api')->select("DELETE FROM brands WHERE id = '$id'");
        if(!$brand)
        {
            return redirect()->back()->with('success', 'Brands Deleted Successfully');
        }else{
            return redirect()->back()->with('error', 'There was a problem deleting this brand');
        }
    }
}
