<?php

namespace Vanguard\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Maatwebsite;
use Maatwebsite\Excel\Facades\Excel;

class BroadcasterManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.broadcaster.index');
    }

    public function braodcasterData(DataTables $dataTables)
    {
        $broadcasters = Utilities::switch_db('api')->select("SELECT * FROM broadcasters");
        $broadcaster_users = [];
        foreach ($broadcasters as $broadcaster){
            $j = 1;
            $user = Utilities::switch_db('api')->select("SELECT * from users where id = '$broadcaster->user_id'");
            $broadcaster_users[] = [
                's_n' => $j,
                'user_id' => $broadcaster->user_id,
                'broadcaster_id' => $broadcaster->id,
                'media_name' => $broadcaster->brand,
                'email' => $user[0]->email,
                'phone' => $user[0]->phone_number
            ];
        }

        return $dataTables->collection($broadcaster_users)
            ->addColumn('details', function ($broadcaster_users) {
                return '<a href="' . route('admin.broadcaster.details', $broadcaster_users['broadcaster_id']) . '" class="btn btn-success btn-xs">  View Details </a>';
            })
            ->addColumn('delete', function ($broadcaster_users) {
                return '<a href="' . route('admin.broadcaster.upload_inventory', $broadcaster_users['broadcaster_id']) . '" class="btn btn-primary btn-xs" > Upload Inventory </button>    ';
            })
            ->rawColumns(['details' => 'details', 'delete' => 'delete'])->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function broadcasterDetails($id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getInventory($id)
    {
//        $hourly_ranges = Utilities::switch_db('api')->select("SELECT * from hourlyRanges");
        $days = Utilities::switch_db('api')->select("SELECT * from days");
        $channels = Utilities::switch_db('api')->select("SELECT * from campaignChannels");
        return view('admin.broadcaster.inventory_create', compact('hourly_ranges', 'days', 'channels', 'id'));
    }

    public function storeInventory(Request $request, $id)
    {
        $now = strtotime(Carbon::now('Africa/Lagos'));
        $this->validate($request, [
            'days' => 'required',
            'channels' => 'required',
            'upload' => 'required'
        ]);

        $user_id = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$id' LIMIT 1");

        $insert = [];
        $price = [];

        //check if the inventory exists
        $check_inventory = Utilities::switch_db('api')->select("SELECT * FROM rateCards where broadcaster = '$id' AND day = '$request->days'");

        if(count($check_inventory) > 0){
            \Session::flash('error', 'Inventory already exist');
            return redirect()->back();
        }

        //insert into rateCards table
        $hourly = Utilities::switch_db('api')
            ->select("SELECT * from hourlyRanges ");

        for($i = 0; $i < count($hourly); $i++)
        {
            $rate[] = [
                'id' => uniqid(),
                'user_id' => $user_id[0]->user_id,
                'broadcaster' => $id,
                'hourly_range_id' => $hourly[$i]->id,
                'day' => $request->days,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
            ];
        }

        $each_save = Utilities::switch_db('api')->table('rateCards')->insert($rate);

        if(!empty($each_save)){
            $extension = \File::extension($request->upload->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {
                $path = \Input::file('upload')->getRealPath();
                $data = Excel::load($path, function($reader) {})->get();
                if(!empty($data) && $data->count()){
                    foreach ($data as $key => $value) {
                        $pp = Utilities::clean_num($value->premium_percent);
                        $p60 = Utilities::clean_num($value->p60secs);
                        $p45 = Utilities::clean_num($value->p45secs);
                        $p30 = Utilities::clean_num($value->p30secs);
                        $p15 = Utilities::clean_num($value->p15secs);
                        $m_age = Utilities::clean_num($value->min_age);
                        $max_a = Utilities::clean_num($value->max_age);
                        $is_p = Utilities::clean_num($value->premium_yn);

                        $target = Utilities::switch_db('api')
                            ->select(
                                "SELECT id from targetAudiences WHERE audience = '$value->target_audience'");

                        $daypart = Utilities::switch_db('api')
                            ->select("SELECT id from dayParts WHERE day_parts = '$value->daypart'");

                        $region = Utilities::switch_db('api')
                            ->select("SELECT id from regions WHERE region = '$value->region'");

                        $adslot_id = uniqid();

                        $gethourly = Utilities::switch_db('api')
                            ->select("SELECT * from hourlyRanges WHERE time_range = '$value->hourly_range'");

                        $h_id = $gethourly[0]->id;

                        $get_rate = Utilities::switch_db('api')->select("SELECT id from rateCards WHERE hourly_range_id = '$h_id' AND day = '$request->days' AND broadcaster = '$id'");

                        $insert[] = [
                            'id' => $adslot_id,
                            'rate_card' => $get_rate[0]->id,
                            'target_audience' => $target[0]->id,
                            'day_parts' => $daypart[0]->id,
                            'region' => $region[0]->id,
                            'from_to_time' => $value->start. ' - ' .$value->stop,
                            'min_age' => (integer) $m_age,
                            'max_age' => (integer) $max_a,
                            'time_created' => date('Y-m-d H:i:s', $now),
                            'time_modified' => date('Y-m-d H:i:s', $now),
                            'broadcaster' => $id,
                            'is_available' => 0,
                            'time_difference' => (strtotime($value->stop)) - (strtotime($value->start)),
                            'time_used' => 0,
                            'channels' => $request->channels
                        ];

                        $price[] = [
                            'id' => uniqid(),
                            'adslot_id' => $adslot_id,
                            'price_60' => $p60,
                            'price_45' => $p45,
                            'price_30' => $p30,
                            'price_15' => $p15,
                            'time_created' => date('Y-m-d H:i:s', $now),
                            'time_modified' => date('Y-m-d H:i:s', $now),
                        ];
                    }

                    if(!empty($insert) && !empty($price)){
                        $each_save = Utilities::switch_db('api')->table('adslots')->insert($insert);
                        $each_save_price = Utilities::switch_db('api')->table('adslotPrices')->insert($price);
                        if($each_save && $each_save_price)
                        {
                            \Session::flash('success', 'Slot created successfully');
                            return redirect()->back();
                        }else{
                            $delete_slot = Utilities::switch_db('api')->delete("DELETE * from adslots where broadcaster = '$id' AND rate_card IN (SELECT id from rateCards where day = '$request->days' AND broadcaster = '$id') ");
                            $delete_rate = Utilities::switch_db('api')->delete("DELETE * from rateCards where day = '$request->days' AND broadcaster = '$id'");
                            \Session::flash('error', 'An error occured');
                            return redirect()->back();
                        }
                    }
                }
            }else {
                \Session::flash('error', 'File must be of type xlsx, xls and csv');
            }
        }else{
            $delete_rate = Utilities::switch_db('api')->delete("DELETE * from rateCards where day = '$request->days' AND broadcaster = '$id'");
            \Session::flash('error', 'An error occured');
            return redirect()->back();
        }


    }

}
