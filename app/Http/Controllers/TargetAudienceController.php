<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Session;

class TargetAudienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $target_audiences = Utilities::switch_db('api')->select("SELECT * FROM targetAudiences where status = 1");
        return view('admin.target_audience.index', compact('target_audiences'));
    }

    public function getData(DataTables $dataTable)
    {
        $target_audiences = Utilities::switch_db('api')->select("SELECT * FROM targetAudiences where status = 1");
        $audience = [];
        $j = 1;
        foreach($target_audiences as $target_audience){
            $audience[] = [
                's_n' => $j,
                'id' => $target_audience->id,
                'name' => $target_audience->audience
            ];

            $j++;
        }
        return $dataTable->collection($audience)
            ->addColumn('edit', function ($audience) {
                return '<button data-toggle="modal" data-target=".edit' . $audience['id'] . '" class="btn btn-primary btn-xs" > Edit </button>';
            })
            ->addColumn('delete', function ($audience) {
                return '<button data-toggle="modal" data-target=".delete' . $audience['id'] . '" class="btn btn-danger btn-xs" > Delete </button>';
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
        return view('admin.target_audience.create');
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
            'audience' => 'required'
        ]);
        $audience = $request->audience;
        $id = uniqid();
        $check_audience = Utilities::switch_db('api')->select("SELECT * from targetAudiences where audience = '$audience'");
        if(count($check_audience) === 1){
            Session::flash('error', 'Target Audience already exists');
            return redirect()->back();
        }

        $insert_audience = Utilities::switch_db('api')->insert("INSERT into targetAudiences (id, audience) VALUES ('$id', '$audience')");

        if($insert_audience){
            Session::flash('success', 'Target Audience added successfully');
            return redirect()->route('target_audience.index');
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
        $audience = Utilities::switch_db('api')->select("SELECT * from targetAudiences where id = '$id'");
        $audience_name = $audience[0]->audience;
        if($audience_name != $request->audience){
            $this->validate($request, [
                'audience' => 'required',
            ]);
            $check_if_exists = Utilities::switch_db('api')->select("SELECT * from targetAudiences where audience = '$request->audience'");
            if(count($check_if_exists) === 1){
                Session::flash('error', 'Target Audience already exist...');
                return redirect()->back();
            }
            $update_audience = Utilities::switch_db('api')->update("UPDATE targetAudiences SET audience = '$request->audience' WHERE id = '$id'");
            if($update_audience){
                Session::flash('success', 'Target Audience updated successfully');
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
        $delete_audience = Utilities::switch_db('api')->delete("DELETE FROM targetAudiences where id = '$id'");
        if($delete_audience){
            Session::flash('success', 'Target Audience deleted');
            return redirect()->back();
        }else{
            Session::flash('error', 'Error occured while processing your request');
            return redirect()->back();
        }
    }
}
