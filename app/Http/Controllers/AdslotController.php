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

        $broadcaster_id = Session::get('broadcaster_id');
        $adslots = $this->getAdslotDetails($broadcaster_id, null);
        return view('broadcaster_module.adslots.index')->with(['adslots' => $adslots, 'broadcaster' => $broadcaster_id]);


    }

    public function adslotData(DataTables $dataTables, Request $request)
    {
        $broadcaster_id = Session::get('broadcaster_id');

        $all_adslots = $this->getAdslotDetails($broadcaster_id, $request->days);

        return $dataTables->collection($all_adslots)
            ->addColumn('edit', function ($all_adslots) {
                return '<a href="#edit_slot'.$all_adslots['id'].'" class="weight_medium modal_click">Edit</a>';
            })
            ->rawColumns(['edit' => 'edit'])->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $preloaded_data = Utilities::getPreloadedData();
        return view('broadcaster_module.adslots.create')->with(['days' => $preloaded_data['days'], 'hours' => $preloaded_data['hourly_ranges'],
                                                                    'regions' => $preloaded_data['regions'], 'target_audiences' => $preloaded_data['target_audience'],
                                                                    'channels' => $preloaded_data['channels'], 'day_parts' => $preloaded_data['day_parts']]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdslotsRequests $request)
    {

        $broadcaster_id = Session::get('broadcaster_id');
        $broadcaster_details = Utilities::getBroadcasterDetails($broadcaster_id);
        $user_id = $broadcaster_details[0]->user_id;
        $rate_card_id = uniqid();
        $time_check = 0;

        for($x = 0; $x < count($request->from_time); $x++){
            $time_difference = (strtotime(Utilities::removeSpace($request->to_time[$x])) - strtotime(Utilities::removeSpace($request->from_time[$x])));
            $time_check += $time_difference;
        }

        if($time_check > 720){
            Session::flash('error', 'Your From To time summation must not exceed 12minutes');
            return redirect()->back();
        }

        $ratecard_array = $this->rateCardArray($rate_card_id, $user_id, $broadcaster_id, $request);

        $adslot_array = $this->adslotsArray($rate_card_id, $request, $broadcaster_id, $broadcaster_details);

        $save_rate = Utilities::switch_db('api')->table('rateCards')->insert($ratecard_array);
        $save_adslot = Utilities::switch_db('api')->table('adslots')->insert($adslot_array);

        if($save_rate && $save_adslot){
            $select_adslots = Utilities::switch_db('api')->select("SELECT id from adslots where rate_card = '$rate_card_id'");
            $price_array = $this->priceArray($select_adslots, $request);
            $save_price = Utilities::switch_db('api')->table('adslotPrices')->insert($price_array);
        }

        if($save_adslot && $save_price && $save_rate){
            Session::flash('success', 'Adslot created successfully...');
            return redirect()->route('adslot.all');
        }else{
            Session::flash('error', 'Error creating adslots, please try again');
            return redirect()->back();
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $adslot)
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
                return response()->json(['success' => 'prices_update']);
            }else{
                return response()->json(['error_no_changes' => 'no_changes']);
            }
        }else{
            $selectAdslotPrice = Utilities::switch_db('api')->select("SELECT * from adslotPrices WHERE adslot_id = '$adslot'");
            if(((int)$request->premium_percent) === 0){
                $deletePremium = Utilities::switch_db('api')->delete("DELETE from adslotPercentages where adslot_id = '$adslot'");
                if($deletePremium){
                    return response()->json(['success_price' => 'prices_update']);
                }
                if($request->premium_percent === "0"){
                    return response()->json(['error_percentage' => 'error_percentage']);
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
                    return response()->json(['success_percentage' => 'percentage_applied']);
                }else{
                    return response()->json(['error_apply_percentage' => 'error_applying_percentage']);
                }
            }else{
                $updatePercentage = Utilities::switch_db('api')->update("UPDATE adslotPercentages SET price_60 = '$premium_60', price_45 = '$premium_45', 
                                                                              price_30 = '$premium_30', price_15 = '$premium_15', percentage = '$request->premium_percent'");
                if($updatePercentage){
                    return response()->json(['success_update_new_percentage' => 'price_update_new_percentage']);
                }else{
                    return response()->json(['error_updating_percentage_price' => 'error_updating_percentage_price']);
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

    public function getAdslotDetails($broadcaster_id, $day)
    {
        $all_adslots = [];
        if($day){
            $adslots = Utilities::switch_db('api')->select("SELECT a.id,d.day,a.from_to_time, p_p.percentage,
                                                            IF(a.id = p_p.adslot_id, p_p.price_60, p.price_60) as price_60,
                                                            IF(a.id = p_p.adslot_id, p_p.price_45, p.price_45) as price_45,
                                                            IF(a.id = p_p.adslot_id, p_p.price_30, p.price_30) as price_30,
                                                            IF(a.id = p_p.adslot_id, p_p.price_15, p.price_15) as price_15
                                                            from adslots as a 
                                                            INNER JOIN adslotPrices as p ON p.adslot_id = a.id
                                                            LEFT JOIN adslotPercentages as p_p ON p_p.adslot_id = a.id
                                                             LEFT JOIN rateCards as r ON r.id = a.rate_card
                                                             LEFT JOIN days as d ON d.id = r.day
                                                             where a.broadcaster = '$broadcaster_id' and d.day LIKE '%$day%'");
        }else{
            $adslots = Utilities::switch_db('api')->select("SELECT a.id, a.from_to_time, d.day, p_p.percentage,
                                                            IF(a.id = p_p.adslot_id, p_p.price_60, p.price_60) as price_60,
                                                            IF(a.id = p_p.adslot_id, p_p.price_45, p.price_45) as price_45,
                                                            IF(a.id = p_p.adslot_id, p_p.price_30, p.price_30) as price_30,
                                                            IF(a.id = p_p.adslot_id, p_p.price_15, p.price_15) as price_15
                                                            from adslots as a 
                                                            INNER JOIN adslotPrices as p ON p.adslot_id = a.id
                                                            LEFT JOIN adslotPercentages as p_p ON p_p.adslot_id = a.id
                                                             LEFT JOIN rateCards as r ON r.id = a.rate_card
                                                             LEFT JOIN days as d ON d.id = r.day
                                                             where a.broadcaster = '$broadcaster_id'");
        }

        foreach ($adslots as $adslot){
            $all_adslots[] = [
                'id' => $adslot->id,
                'day' => $adslot->day,
                'time_slot' => $adslot->from_to_time,
                '60_seconds' => $adslot->price_60,
                '45_seconds' => $adslot->price_45,
                '30_seconds' => $adslot->price_30,
                '15_seconds' => $adslot->price_15,
                'percentage' => $adslot->percentage
            ];
        }

        return $all_adslots;
    }

    public function rateCardArray($rate_card_id, $user_id, $broadcaster_id, $request)
    {
        $ratecard_array = [
            'id' => $rate_card_id,
            'user_id' => $user_id,
            'broadcaster' => $broadcaster_id,
            'day' => $request->days,
            'hourly_range_id' => $request->hourly_ranges,
        ];

        return $ratecard_array;
    }

    public function adslotsArray($rate_card_id, $request, $broadcaster_id, $broadcaster_details)
    {
        $adslot_array = [];
        for ($i = 0; $i < count($request->regions); $i++){
            $adslot_array[] = [
                'id'=> uniqid(),
                'rate_card' => $rate_card_id,
                'target_audience' => $request->target_audiences[$i],
                'day_parts' => $request->dayparts[$i],
                'region' => $request->regions[$i],
                'from_to_time' => Utilities::removeSpace($request->from_time[$i]). ' - '.Utilities::removeSpace($request->to_time[$i]),
                'min_age' => $request->min_age[$i],
                'max_age' => $request->max_age[$i],
                'broadcaster' => $broadcaster_id,
                'is_available' => 0,
                'time_difference' => (strtotime(Utilities::removeSpace($request->to_time[$i]))) - (strtotime(Utilities::removeSpace($request->from_time[$i]))),
                'time_used' => 0,
                'channels' => $broadcaster_details[0]->channel_id,
            ];
        }

        return $adslot_array;
    }

    public function priceArray($select_adslots, $request)
    {
        $price = [];
        for ($j = 0; $j < count($select_adslots); $j++){
            $price[] = [
                'id' => uniqid(),
                'adslot_id' => $select_adslots[$j]->id,
                'price_60' => $request->price_60[$j],
                'price_45' => $request->price_45[$j],
                'price_30' => $request->price_30[$j],
                'price_15' => $request->price_15[$j],
            ];
        }

        return $price;
    }


}
