<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Http\Requests\StoreWalkins;
use Yajra\DataTables\DataTables;
use Vanguard\Libraries\Utilities;
use Session;

class WalkinsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $walkins = Utilities::switch_db('api')->select("SELECT * from users WHERE id IN (SELECT user_id from walkIns WHERE broadcaster_id = '$broadcaster_id')");
        return view('walkins.index')->with('walkins', $walkins);
    }

    public function walkinsData(DataTables $dataTables)
    {
        $j = 1;
        $broad_walkins = [];
        $broadcaster_id = Session::get('broadcaster_id');
        $walkins = Utilities::switch_db('api')->select("SELECT * from users WHERE id IN (SELECT user_id from walkIns WHERE broadcaster_id = '$broadcaster_id' AND status = 0) ORDER BY time_created DESC");
        foreach ($walkins as $walkin){
            $broad_walkins[] = [
                'id' => $j,
                'full_name' => $walkin->firstname.' '.$walkin->lastname,
                'email' => $walkin->email,
                'phone' => $walkin->phone_number,
                'user_id' => $walkin->id,
            ];
            $j++;
        }

        return $dataTables->collection($broad_walkins)
            ->addColumn('campaign', function ($broad_walkins) {
                return '<a href="' . route('campaign.create2', $broad_walkins['user_id']) .'" class="btn btn-success btn-xs"> Create Campaign </a>';
            })
            ->addColumn('delete', function($broad_walkins){
                return '<button data-toggle="modal" data-target=".deleteModal' . $broad_walkins['user_id']. '" class="btn btn-danger btn-xs" > Delete </button>    ';
            })
            ->rawColumns(['campaign' => 'campaign', 'delete' => 'delete'])->addIndexColumn()
            ->make(true);

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
    public function store(StoreWalkins $request)
    {
        $user_id = uniqid();
        $broadcaster_id = Session::get('broadcaster_id');
        $role_id = Utilities::switch_db('api')->select("SELECT role_id from users WHERE id IN ( SELECT user_id from broadcasters WHERE id = '$broadcaster_id')");
        $insert_user = [
            'id' => $user_id,
            'role_id' => $role_id[0]->role_id,
            'email' => $request->email,
            'password' => bcrypt('password'),
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'phone_number' => $request->phone_number,
            'user_type' => 5
        ];

        $insert_walkin = [
            'id' => uniqid(),
            'broadcaster_id' => $broadcaster_id,
            'user_id' => $user_id,
            'client_type_id' => 1,
        ];
        $check_user = Utilities::switch_db('api')->select("SELECT * from users where email = '$request->email'");
        if(count($check_user) === 1){
            Session::flash('error', 'Email address already exist');
            return redirect()->back();
        }
        $saveUser = Utilities::switch_db('api')->table('users')->insert($insert_user);
        $saveWalkins = Utilities::switch_db('api')->table('walkIns')->insert($insert_walkin);

        if($saveUser && $saveWalkins){
            Session::flash('success', 'Walk-In created successfully');
            return redirect()->route('walkins.all');
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
//        $deleteUser = Utilities::switch_db('api')->delete("DELETE from users WHERE id = '$id'");
        $deleteWalkins = Utilities::switch_db('api')->update("UPDATE walkIns set status = 1 where user_id = '$id'");
        if($deleteWalkins){
            Session::flash('success', 'Walk-In deleted successfully...');
            return redirect()->back();
        }else{
            Session::flash('error', 'Error deleting Walk-In...');
            return redirect()->back();
        }


    }
}
