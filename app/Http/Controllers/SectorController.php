<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Api;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Api::get_sectors();
        $sectors = json_decode($sectors);
        $all_sectors = $sectors->data;

        return view('sector.index')->with('sectors', $all_sectors);
    }

    public function create()
    {
        return view('sector.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'sector_code' => 'required'
        ]);

        $name = $request->name;
        $sector_code = $request->sector_code;

        $create_sector = Api::create_sector($name, $sector_code);

        return view('sector.index');
    }

    public function destroy($sector_id)
    {
        Api::delete_sector($sector_id);
    }

    public function createsubSector(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'sector_code' => 'required',
            'sector_id' => 'required'
        ]);

        $name = $request->name;
        $sector_code = $request->sector_code;
        $sector_id = $request->sector_id;

        $create_subSector = Api::create_sector($name, $sector_code, $sector_id);

        return view('sector.index');
    }

    public function removeSubsector(Request $request)
    {
        $this->validate($request, [
            'sector_id' => 'required',
            'sub_sector_id' => 'required'
        ]);

        $sector_id = $request->sector_id;
        $sub_sector_id = $request->sub_sector_id;

        $remove_subsector = Api::remove_sub_sector($sector_id, $sub_sector_id);
    }
}
