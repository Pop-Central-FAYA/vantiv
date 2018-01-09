<?php

namespace Vanguard\Http\Controllers\Agency;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;

class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $broadcaster = \Session::get('agency_id');
        return view('agency.campaigns.all_campaign');
    }

    public function getAllData(Datatables $datatables, Request $request)
    {
        $campaign = [];
        $j = 1;
        $broadcaster = Session::get('broadcaster_id');
        $all_campaign = Utilities::switch_db('api')->select("SELECT * from campaigns WHERE broadcaster = '$broadcaster' AND adslots > 0 ORDER BY time_created asc");
        foreach ($all_campaign as $cam)
        {
            $campaign[] = [
                'id' => $j,
                'name' => $cam->name,
                'brand' => $cam->brand,
                'product' => $cam->product,
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'adslots' => $cam->adslots,
                'compliance' => '86%',
                'status' => 'True'
            ];
            $j++;
        }

        return $datatables->collection($campaign)
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
