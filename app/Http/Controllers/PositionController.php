<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Session;

class PositionController extends Controller
{
    public function allPostion()
    {
        $broadcaster = Session::get('broadcaster_id');
        $all_positions = Utilities::switch_db('api')->select("SELECT * from filePositions where broadcaster_id = '$broadcaster' AND status = 0");
        return view('file_position.index', compact('all_positions'));
    }

    public function createPosition()
    {
        return view('file_position.create');
    }

    public function storePosition(Request $request)
    {
        $broadcaster = Session::get('broadcaster_id');
        $this->validate($request, [
            'position' => 'required',
            'percentage' => 'required',
        ]);

        if(!is_numeric($request->percentage) || ((int)$request->percentage < 1)){
            Session::flash('error', 'Percentage must be a number starting from 1');
            return redirect()->back();
        }

        $position = Utilities::formatString($request->position);

        $check_existing = Utilities::switch_db('api')->select("SELECT * from filePositions where position = '$position' AND broadcaster_id = '$broadcaster'");

        if(count($check_existing) > 1){
            Session::flash('error', 'You have already set this position');
            return redirect()->back();
        }

        $insert_position = [
            'id' => uniqid(),
            'position' => $position,
            'percentage' => $request->percentage,
            'broadcaster_id' => $broadcaster,
        ];

        $insert = Utilities::switch_db('api')->table('filePositions')->insert($insert_position);

        if($insert){
            Session::flash('success', 'Position created successfully');
            return redirect()->route('position.list');
        }else{
            Session::flash('error', 'An error occurred while creating this position');
            return redirect()->back();
        }
    }


    public function updatePosition(Request $request, $id)
    {
        $broadcaster = Session::get('broadcaster_id');
        $this->validate($request, [
            'position' => 'required',
            'percentage' => 'required',
        ]);

        if(!is_numeric($request->percentage) || ((int)$request->percentage < 1)){
            Session::flash('error', 'Percentage must be a number starting from 1');
            return redirect()->back();
        }

        $position = Utilities::formatString($request->position);

        $percentage = (int)$request->percentage;

        $check_existing = Utilities::switch_db('api')->select("SELECT * from filePositions where id = '$id' AND broadcaster_id = '$broadcaster'");

        if($check_existing[0]->position === $position && $check_existing[0]->percentage === $percentage){
            Session::flash('info', 'No changes made...');
            return redirect()->back();
        }

        $check = Utilities::switch_db('api')->select("SELECT * from filePositions WHERE position = '$position' AND broadcaster_id = '$broadcaster'");
//        dd($check);
        if($check[0]->position === $position && $check[0]->percentage === $percentage){
            Session::flash('error', 'You have already set this position');
            return redirect()->back();
        }
        if($check[0]->position === $position && $check[0]->percentage === $percentage){
            $update_position = Utilities::switch_db('api')->update("UPDATE filePositions set position = '$position', percentage = '$request->percentage' WHERE id = '$id' AND broadcaster_id = '$broadcaster'");
            if($update_position){
                Session::flash('success', 'Update was successful');
                return redirect()->back();
            }else{
                Session::flash('error', 'An error occurred while processing your request');
                return redirect()->back();
            }
        }

        $update_position = Utilities::switch_db('api')->update("UPDATE filePositions set position = '$position', percentage = '$request->percentage' WHERE id = '$id' AND broadcaster_id = '$broadcaster'");
        if($update_position){
            Session::flash('success', 'Update was successful');
            return redirect()->back();
        }else{
            Session::flash('error', 'An error occurred while processing your request');
            return redirect()->back();
        }

    }

    public function deletePosition($id)
    {
        $broadcaster = Session::get('broadcaster_id');

        $delete_position = Utilities::switch_db('api')->update("UPDATE filePositions set status = 1 WHERE id = '$id' AND broadcaster_id = '$broadcaster'");

        if($delete_position){
            Session::flash('success', 'Position deleted successfully...');
            return redirect()->back();
        }else{
            Session::flash('error', 'An error occurred while processing your request...');
            return redirect()->back();
        }
    }
}
