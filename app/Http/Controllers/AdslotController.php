<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Vanguard\Http\Requests\StoreAdslotsRequests;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Maths;
use League\Flysystem\Exception;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;


class AdslotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $broadcaster = Session::get('broadcaster_id');
        $broadcaster_user = Session::get('broadcaster_user_id');
        if(!Session::get('broadcaster_id')){
            $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
            $broadcaster = $broadcaster_id[0]->broadcaster_id;
        }
        $adslots = Utilities::switch_db('api')->select("SELECT * from adslots where broadcaster = '$broadcaster'");
        $region = Utilities::switch_db('api')->select("SELECT * from regions");
        $all_adslot = [];
        $j = 1;
        foreach ($adslots as $adslot){
            $premium_prices = Utilities::switch_db('api')->select("SELECT * from adslotPercentages WHERE adslot_id = '$adslot->id'");
            if(count($premium_prices) != 0){
                $price_60 = $premium_prices[0]->price_60;
                $price_45 = $premium_prices[0]->price_45;
                $price_30 = $premium_prices[0]->price_30;
                $price_15 = $premium_prices[0]->price_15;
                $percentage = $premium_prices[0]->percentage;
            }else{
                $prices = Utilities::switch_db('api')->select("SELECT * from adslotPrices where adslot_id = '$adslot->id'");
                $price_60 = $prices[0]->price_60;
                $price_45 = $prices[0]->price_45;
                $price_30 = $prices[0]->price_30;
                $price_15 = $prices[0]->price_15;
                $percentage = 0;
            }
            $day = Utilities::switch_db('api')->select("SELECT `day` as this_day from days where id IN(SELECT day from rateCards where id = '$adslot->rate_card')");
            $all_adslot[] = [
                's_n' => $j,
                'id' => $adslot->id,
                'day' => $day[0]->this_day,
                'time_slot' => $adslot->from_to_time,
                '60_seconds' => $price_60,
                '45_seconds' => $price_45,
                '30_seconds' => $price_30,
                '15_seconds' => $price_15,
                'percentage' => $percentage,
            ];
            $j++;
        }

        $broadcaster = Session::get('broadcaster_id');
        if(Session::get('broadcaster_user_id')){
            $br_user_id = Session::get('broadcaster_user_id');
            $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$br_user_id'");
            $broadcaster = $broadcaster_id[0]->broadcaster_id;
        }
        $all_positions = Utilities::switch_db('api')->select("SELECT * from filePositions where broadcaster_id = '$broadcaster' AND status = 0");
        return view('adslot.index')->with('adslots', $all_adslot)->with('broadcaster', $broadcaster)->with('regions', $region)->with('all_positions', $all_positions);
    }

    public function adslotData(DataTables $dataTables)
    {
        $broadcaster = Session::get('broadcaster_id');
        $broadcaster_user = Session::get('broadcaster_user_id');
        if(!Session::get('broadcaster_id')){
            $broadcaster_id = Utilities::switch_db('api')->select("SELECT broadcaster_id from broadcasterUsers where id = '$broadcaster_user'");
            $broadcaster = $broadcaster_id[0]->broadcaster_id;
        }
        $adslots = Utilities::switch_db('api')->select("SELECT * from adslots where broadcaster = '$broadcaster'");
        $all_adslot = [];
        $j = 1;
        foreach ($adslots as $adslot){
            $premium_prices = Utilities::switch_db('api')->select("SELECT * from adslotPercentages WHERE adslot_id = '$adslot->id'");
            if(count($premium_prices) != 0){
                $price_60 = '&#8358;'.number_format($premium_prices[0]->price_60,2);
                $price_45 = '&#8358;'.number_format($premium_prices[0]->price_45,2);
                $price_30 = '&#8358;'.number_format($premium_prices[0]->price_30,2);
                $price_15 = '&#8358;'.number_format($premium_prices[0]->price_15,2);
            }else{
                $prices = Utilities::switch_db('api')->select("SELECT * from adslotPrices where adslot_id = '$adslot->id'");
                $price_60 = '&#8358;'.number_format($prices[0]->price_60,2);
                $price_45 = '&#8358;'.number_format($prices[0]->price_45,2);
                $price_30 = '&#8358;'.number_format($prices[0]->price_30,2);
                $price_15 = '&#8358;'.number_format($prices[0]->price_15,2);
            }
            $day = Utilities::switch_db('api')->select("SELECT `day` as this_day from days where id IN(SELECT day from rateCards where id = '$adslot->rate_card')");
            $all_adslot[] = [
                's_n' => $j,
                'id' => $adslot->id,
                'day' => $day[0]->this_day,
                'time_slot' => $adslot->from_to_time,
                '60_seconds' => $price_60,
                '45_seconds' => $price_45,
                '30_seconds' => $price_30,
                '15_seconds' => $price_15,
            ];
            $j++;
        }
        if(Session::get('broadcaster_id')) {
            return $dataTables->collection($all_adslot)
                ->addColumn('edit', function ($all_adslot) {

                    return '<button data-toggle="modal" data-target=".editModal' . $all_adslot['id'] . '" class="btn btn-primary btn-xs" > Edit </button>    ';

                })
                ->rawColumns(['edit' => 'edit'])->addIndexColumn()
                ->make(true);
        }else{
            return $dataTables->collection($all_adslot)
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $day = Utilities::switch_db('api')->select("SELECT * from days");
        $hourly = Utilities::switch_db('api')->select("SELECT * from hourlyRanges");
        $region = Utilities::switch_db('api')->select("SELECT * from regions");
        $target = Utilities::switch_db('api')->select("SELECT * from targetAudiences");
        $daypart = Utilities::switch_db('api')->select("SELECT * from dayParts");
        $channels = Utilities::switch_db('api')->select("SELECT * from campaignChannels");
        return view('adslot.create')->with(['days' => $day, 'hours' => $hourly, 'regions' => $region, 'targets' => $target, 'channels' => $channels, 'day_parts' => $daypart]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdslotsRequests $request)
    {
        $broadcaster = Session::get('broadcaster_id');
        $user_id = Utilities::switch_db('api')->select("SELECT user_id from broadcasters where id = '$broadcaster'");
        $rate_id = uniqid();
        $adslot_id = uniqid();
        $insert = [];
        $price = [];
        $time_check = [];
        $h = 0; $i = 0; $j = 0; $k = 0; $l = 0; $m = 0; $n = 0; $o = 0; $p = 0; $q = 0;
        $r = 0; $s = 0; $t = 0; $w = 0;
        $ratecard = [
            'id' => $rate_id,
            'user_id' => $user_id[0]->user_id,
            'broadcaster' => $broadcaster,
            'day' => $request->days,
            'hourly_range_id' => $request->hourly_range,
        ];
//        dd($request->all());
        for($x = 0; $x < count($request->from_time); $x++){
            $diff = (strtotime($request->to_time[$x]) - strtotime($request->from_time[$x]));
            $time_check[] = $diff;
        }

        if((array_sum($time_check)) > 720){
            Session::flash('error', 'Your From To time summation must not exceed 12minutes');
            return redirect()->back();
        }

        $save_rate = Utilities::switch_db('api')->table('rateCards')->insert($ratecard);

        foreach ($request->from_time as $r){
            $insert[] = [
                'id' => uniqid(),
                'rate_card' => $rate_id,
                'target_audience' => $request->target_audience[$h++],
                'day_parts' => $request->dayparts[$i++],
                'region' => $request->region[$j++],
                'from_to_time' => $request->from_time[$k++]. ' - ' .$request->to_time[$p++],
                'min_age' => $request->min_age[$m++],
                'max_age' => $request->max_age[$n++],
                'broadcaster' => $broadcaster,
                'is_available' => 0,
                'time_difference' => (strtotime($request->to_time[$l++])) - (strtotime($request->from_time[$o++])),
                'time_used' => 0,
                'channels' => $request->channel,
            ];
        }

        $save_adslot = Utilities::switch_db('api')->table('adslots')->insert($insert);
        $select_adslot = Utilities::switch_db('api')->select("SELECT id from adslots where rate_card = '$rate_id'");

        foreach ($select_adslot as $p){
            $price[] = [
                'id' => uniqid(),
                'adslot_id' => $p->id,
                'price_60' => $request->price_60[$w++],
                'price_45' => $request->price_45[$q++],
                'price_30' => $request->price_30[$t++],
                'price_15' => $request->price_15[$s++],
            ];
        }

        $save_price = Utilities::switch_db('api')->table('adslotPrices')->insert($price);

        if($save_adslot && $save_price && $save_rate){
            Session::flash('success', 'Adslot created successfully...');
            return back();
        }else{
            Session::flash('error', 'Error creating adslots, please try again');
            return back();
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $broadcaster, $adslot)
    {
        $premium = [];
        $this->validate($request, [
            'time_60' => 'required',
            'time_45' => 'required',
            'time_30' => 'required',
            'time_15' => 'required'
        ]);

        if($request->premium_percent === ""){
            $adslotPrice = Utilities::switch_db('api')->update("UPDATE adslotPrices SET price_60 = '$request->time_60', price_45 = '$request->time_45', 
                                                                    price_30 = '$request->time_30', price_15 = '$request->time_15' WHERE adslot_id = '$adslot'");
            if($adslotPrice){
                Session::flash('success', 'Prices updated for this slot');
                return back();
            }else{
                Session::flash('error', 'Error updating adslot price');
                return back();
            }
        }else{
            $selectAdslotPrice = Utilities::switch_db('api')->select("SELECT * from adslotPrices WHERE adslot_id = '$adslot'");
            if(((int)$request->premium_percent) === 0){
                $deletePremium = Utilities::switch_db('api')->delete("DELETE from adslotPercentages where adslot_id = '$adslot'");
                if($deletePremium){
                    Session::flash('success', 'Percentage price deleted for this slot');
                    return back();
                }
                if($request->premium_percent === "0"){
                    Session::flash('error', 'You cannot apply this percentage');
                    return back();
                }
            }else{
                $premium_60 = ($selectAdslotPrice[0]->price_60 + (((int)$request->premium_percent) / 100) * $selectAdslotPrice[0]->price_60);
                $premium_45 = ($selectAdslotPrice[0]->price_45 + (((int)$request->premium_percent) / 100) * $selectAdslotPrice[0]->price_45);
                $premium_30 = ($selectAdslotPrice[0]->price_30 + (((int)$request->premium_percent) / 100) * $selectAdslotPrice[0]->price_30);
                $premium_15 = ($selectAdslotPrice[0]->price_15 + (((int)$request->premium_percent) / 100) * $selectAdslotPrice[0]->price_15);
            }

            $premiumPrice = Utilities::switch_db('api')->select("SELECT * from adslotPercentages where adslot_id = '$adslot'");
            if(count($premiumPrice) === 0){
                $premium[] = [
                    'id' => uniqid(),
                    'adslot_id' => $adslot,
                    'price_60' => $premium_60,
                    'price_45' => $premium_45,
                    'price_30' => $premium_30,
                    'price_15' => $premium_15,
                    'percentage' => $request->premium_percent
                ];

                $creatPremium = Utilities::switch_db('api')->table('adslotPercentages')->insert($premium);
                if($creatPremium){
                    Session::flash('success', 'Percentage applied to prices successfully...');
                    return back();
                }else{
                    Session::flash('error', 'Error applying percentage to price');
                    return back();
                }
            }else{
                $updatePercentage = Utilities::switch_db('api')->update("UPDATE adslotPercentages SET price_60 = '$premium_60', price_45 = '$premium_45', 
                                                                              price_30 = '$premium_30', price_15 = '$premium_15', percentage = '$request->premium_percent'");
                if($updatePercentage){
                    Session::flash('success', 'Prices updated with the new percentage...');
                    return back();
                }else{
                    Session::flash('error', 'Error updating price with the new percentage...');
                    return back();
                }
            }

        }
    }

    public function getAdslotByRegion($region_d)
    {
        //        api for adslot
        $ratecard = Api::get_adslot_by_region($region_d);
        $a = (json_decode($ratecard)->data);
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        $seconds = [60, 45, 39, 15];
        $preload_ratecard = Api::get_ratecard_preloaded();
        $load = $preload_ratecard->data;
        return view('adslot.index')->with('ratecard', $a)->with('seconds', $seconds)->with('preload', $load);
    }


}
