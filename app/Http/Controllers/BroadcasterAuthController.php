<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Country;
use Illuminate\Http\Request;
use Vanguard\Http\Requests\StoreBroadcasterUser;
use Vanguard\Libraries\Utilities;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Vanguard\Mail\SendConfirmationMail;
use Yajra\DataTables\DataTables;
use Mail;

class BroadcasterAuthController extends Controller
{
    public function getRegister()
    {
        $countries = Country::all();
        $sectors = Utilities::switch_db('api')->select("SELECT * FROM sectors");

        return view('broadcaster_onboard.onboard')
            ->with('countries', $countries)
            ->with('sectors', $sectors);
    }

    public function postRegister(Request $request)
    {

    }

    public function allUser()
    {
        $broadcaster = Session::get('broadcaster_id');
        $all_users = Utilities::switch_db('api')->select("SELECT * from users where id IN(SELECT user_id from broadcasterUsers where broadcaster_id = '$broadcaster')");
        return view('broadcaster.index', compact('all_users'));
    }

    public function createUser()
    {
        $countries = Country::all();
        return view('broadcaster.create')->with('countries', $countries);
    }

    public function userData(DataTables $dataTables)
    {

        $users = [];
        $j = 1;
        $broadcaster = Session::get('broadcaster_id');
        $all_users = Utilities::switch_db('api')->select("SELECT * from users where id IN(SELECT user_id from broadcasterUsers where broadcaster_id = '$broadcaster') AND status = 1");
        foreach ($all_users as $all_user){
            $users[] = [
                's_n' => $j,
                'id' => $all_user->id,
                'name' => $all_user->firstname.' '.$all_user->lastname,
                'email' => $all_user->email,
                'phone' => $all_user->phone_number,
            ];
            $j++;
        }

        return $dataTables->collection($users)
            ->addColumn('delete', function ($users) {
                return '<button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal' . $users['id'] . '" style="font-size: 16px">
                            Delete
                        </button>';
            })
            ->rawColumns(['delete' => 'delete'])->addIndexColumn()
            ->make(true);
    }

    public function postBroadcasterUser(StoreBroadcasterUser $request)
    {

    }

    public function deleteBroadcasterUser($id)
    {
        $user = Utilities::switch_db('api')->select("SELECT * from users where id = '$id'");
        $email = $user[0]->email;
        $update_api_user_table = Utilities::switch_db('api')->update("UPDATE users set status = 0 where id = '$id'");
        $update_local_user_db = DB::update("UPDATE users set status = 'Banned' where email = '$email'");
        if($update_local_user_db && $update_api_user_table){
            Session::flash('success', 'User Deleted successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'Problem occur while deleting this user');
            return redirect()->back();
        }

    }

}
