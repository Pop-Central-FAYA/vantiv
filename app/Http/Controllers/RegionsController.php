<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Session;

class RegionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = Utilities::switch_db('api')->select("SELECT * from regions");
        return view('admin.regions.index', compact('regions'));
    }

    public function data(DataTables $dataTables)
    {
        $all_regions = Utilities::switch_db('api')->select("SELECT * FROM regions where status = 1");
        $regions = [];
        $j = 1;
        foreach($all_regions as $all_region){
            $regions[] = [
                's_n' => $j,
                'id' => $all_region->id,
                'name' => $all_region->region
            ];

            $j++;
        }
        return $dataTables->collection($regions)
            ->addColumn('edit', function ($regions) {
                return '<button data-toggle="modal" data-target=".edit' . $regions['id'] . '" class="btn btn-primary btn-xs" > Edit </button>';
            })
            ->addColumn('delete', function ($regions) {
                return '<button data-toggle="modal" data-target=".delete' . $regions['id'] . '" class="btn btn-danger btn-xs" > Delete </button>';
            })
            ->rawColumns(['edit' => 'edit', 'delete' => 'delete'])->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.regions.create');
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
            'region' => 'required',
        ]);
        $region = $request->region;
        $id = uniqid();
        $check_region = Utilities::switch_db('api')->select("SELECT * from regions where region = '$region'");
        if(count($check_region) === 1){
            Session::flash('error', 'Region already exists');
            return redirect()->back();
        }

        $insert_region = Utilities::switch_db('api')->insert("INSERT into regions (id, region) VALUES ('$id', '$region')");

        if($insert_region){
            Session::flash('success', 'Region added successfully');
            return redirect()->route('admin.region.index');
        }

        Session::flash('error', 'Error occured while processing your request');
        return redirect()->back();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $region = Utilities::switch_db('api')->select("SELECT * from regions where id = '$id'");
        $region_name = $region[0]->region;
        if($region_name != $request->region){
            $this->validate($request, [
                'region' => 'required',
            ]);
            $check_if_exists = Utilities::switch_db('api')->select("SELECT * from regions where region = '$request->region'");
            if(count($check_if_exists) === 1){
                Session::flash('error', 'Region already exist...');
                return redirect()->back();
            }
            $update_region = Utilities::switch_db('api')->update("UPDATE regions SET region = '$request->region' WHERE id = '$id'");
            if($update_region){
                Session::flash('success', 'Region updated successfully');
                return redirect()->back();
            }

            Session::flash('error', 'Error occured while processing your request');
            return redirect()->back();
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
        $delete_region = Utilities::switch_db('api')->delete("DELETE FROM regions where id = '$id'");
        if($delete_region){
            Session::flash('success', 'Region deleted');
            return redirect()->back();
        }else{
            Session::flash('error', 'Error occured while processing your request');
            return redirect()->back();
        }
    }
}
