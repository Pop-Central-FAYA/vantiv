<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Api;
use Illuminate\Http\Request;

class DayPartController extends Controller
{
    public function index()
    {
        $dayParts = json_decode(Api::get_dayParts())->data;

        return view('campaign.daypart.index')->with('dayParts', $dayParts);
    }

    public function create()
    {
        return view('campaign.daypart.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'day_part' => 'required'
        ]);

        $day_part = $request->day_time;

        $create = Api::create_daypart($day_part);

        session()->flash('success', 'Day Part Successfully created');

        return redirect('dayparts.index');
    }


}
