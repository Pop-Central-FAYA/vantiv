<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Session;

class AdminDayPartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dayparts = Utilities::switch_db('api')->select("SELECT * from dayParts");
        return view('admin.day-parts.index', compact('dayparts'));
    }

    public function getData(DataTables $dataTables)
    {
        $all_dayparts = Utilities::switch_db('api')->select("SELECT * FROM dayParts where status = 1");
        $dayparts = [];
        $j = 1;
        foreach($all_dayparts as $all_daypart){
            $dayparts[] = [
                's_n' => $j,
                'id' => $all_daypart->id,
                'name' => $all_daypart->day_parts
            ];

            $j++;
        }
        return $dataTables->collection($dayparts)
            ->addColumn('edit', function ($dayparts) {
                return '<button data-toggle="modal" data-target=".edit' . $dayparts['id'] . '" class="btn btn-primary btn-xs" > Edit </button>';
            })
            ->addColumn('delete', function ($dayparts) {
                return '<button data-toggle="modal" data-target=".delete' . $dayparts['id'] . '" class="btn btn-danger btn-xs" > Delete </button>';
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
        return view('admin.day-parts.create');
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
            'day_part' => 'required'
        ]);

        $daypart = $request->day_part;
        $id = uniqid();
        $check_daypart = Utilities::switch_db('api')->select("SELECT * from dayParts where day_parts = '$daypart'");
        if(count($check_daypart) === 1){
            Session::flash('error', 'Day Part already exists');
            return redirect()->back();
        }

        $insert_daypart = Utilities::switch_db('api')->insert("INSERT into dayParts (id, day_parts) VALUES ('$id', '$daypart')");

        if($insert_daypart){
            Session::flash('success', 'Day Part added successfully');
            return redirect()->route('admin.dayparts');
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
        $day_parts = Utilities::switch_db('api')->select("SELECT * from dayParts where id = '$id'");
        $daypart_name = $day_parts[0]->day_parts;
        if($daypart_name != $request->day_part){
            $this->validate($request, [
                'day_part' => 'required',
            ]);
            $check_if_exists = Utilities::switch_db('api')->select("SELECT * from dayParts where day_parts = '$request->day_part'");
            if(count($check_if_exists) === 1){
                Session::flash('error', 'Day Part already exist...');
                return redirect()->back();
            }
            $update_day_part = Utilities::switch_db('api')->update("UPDATE dayParts SET day_parts = '$request->day_part' WHERE id = '$id'");
            if($update_day_part){
                Session::flash('success', 'Day Part updated successfully');
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
        $delete_day_parts = Utilities::switch_db('api')->delete("DELETE FROM dayParts where id = '$id'");
        if($delete_day_parts){
            Session::flash('success', 'Day Part deleted');
            return redirect()->back();
        }else{
            Session::flash('error', 'Error occured while processing your request');
            return redirect()->back();
        }
    }
}
