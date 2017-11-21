<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Api;

class WalkinsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $walkins_all = json_decode(Api::get_walkins());
        $b = (object)($walkins_all->data);
        return view('walkins.index')->with('walkin', $b);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('walkins.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $walkins_add = Api::add_walkins($request);
        $walkins = json_decode($walkins_add);
        if($walkins->status === false)
        {
            return redirect()->back()->with('error', $walkins->message);
        }else{
            return redirect()->back()->with('success', trans('app.walkins'));
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
        $id = $id;
        $walkins = Api::delete_walkins($id);
        $wal = json_decode($walkins);
        if($wal->status === false)
        {
            return redirect()->back()->with('error', $wal->message);
        }else{
            return redirect()->back()->with('success', trans('app.walkins_delete'));
            return redirect()->back();
        }

    }
}
