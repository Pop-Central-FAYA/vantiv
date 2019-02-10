<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Vanguard\Http\Requests\StoreAdslotsRequests;
use Vanguard\Http\Requests\UpdateAdslotsRequest;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Maths;
use League\Flysystem\Exception;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\Adslot\Adslotlist;
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
        $company_id = \Auth::user()->companies->first()->id;
        $adslots_list_service = new Adslotlist($company_id, null);
        $adslots = $adslots_list_service->adlotsLists();
        return view('broadcaster_module.adslots.index')->with(['adslots' => $adslots, 'broadcaster' => $company_id]);


    }

    public function adslotData(DataTables $dataTables, Request $request)
    {
        $company_id = \Auth::user()->companies->first()->id;
        $adslots_list_service = new Adslotlist($company_id, $request->day);
        $adslots = $adslots_list_service->adlotsLists();
        return $dataTables->collection($adslots)
            ->addColumn('edit', function ($adslots) {
                return '<a href="#edit_slot'.$adslots['id'].'" class="weight_medium modal_click">Edit</a>';
            })
            ->rawColumns(['edit' => 'edit'])->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        $preloaded_data = Utilities::getPreloadedData();
        return view('broadcaster_module.adslots.create')->with(['days' => $preloaded_data['days'], 'hours' => $preloaded_data['hourly_ranges'],
                                                                    'regions' => $preloaded_data['regions'], 'target_audiences' => $preloaded_data['target_audience'],
                                                                    'channels' => $preloaded_data['channels'], 'day_parts' => $preloaded_data['day_parts']]);
    }

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
            Session::flash('error', 'You have exceeded the 12 minutes break for this hour');
            return redirect()->back();
        }

        $check_ratecard = Utilities::checkRatecardExistence($broadcaster_id, $request->hourly_ranges, $request->days);
        if($check_ratecard){
            $message = 'Your time of '.(($time_check) / 60).' is above the allowed max of 12 minutes per hour';
            Session::flash('error', $message);
            return redirect()->back();
        }

        $ratecard_array = $this->rateCardArray($rate_card_id, $user_id, $broadcaster_id, $request);

        $adslot_array = $this->adslotsArray($rate_card_id, $request, $broadcaster_id, $broadcaster_details);
        Utilities::switch_db('api')->beginTransaction();
        try {
            $save_rate = Utilities::switch_db('api')->table('rateCards')->insert($ratecard_array);
        }catch (\Exception $e) {
            Utilities::switch_db('api')->rollback();
            $message = $e->getMessage();
            Session::flash('error', $message);
            return redirect()->back();
        }

        try {
            $save_adslot = Utilities::switch_db('api')->table('adslots')->insert($adslot_array);
        }catch (\Exception $e) {
            Utilities::switch_db('api')->rollback();
            $message = $e->getMessage();
            Session::flash('error', $message);
            return redirect()->back();
        }

        $select_adslots = Utilities::switch_db('api')->select("SELECT id from adslots where rate_card = '$rate_card_id'");
        $price_array = $this->priceArray($select_adslots, $request);
        try {
            $save_price = Utilities::switch_db('api')->table('adslotPrices')->insert($price_array);
        }catch (\Exception $e) {
            Utilities::switch_db('api')->rollback();
            $message = $e->getMessage();
            Session::flash('error', $message);
            return redirect()->back();
        }

        Utilities::switch_db('api')->commit();
        Session::flash('success', 'Adslot created successfully...');
        return redirect()->route('adslot.all');

    }

    public function update(UpdateAdslotsRequest $request, $adslot)
    {

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
                }else{
                    return response()->json(['error_percentage' => 'error_percentage']);
                }
            }

            $premiumPrice = $this->getAdslotPercentages($adslot);

            if(count($premiumPrice) === 0){

                $premium = $this->premiumPrices($selectAdslotPrice, $request, $adslot);

                $creatPremium = Utilities::switch_db('api')->table('adslotPercentages')->insert($premium);

                if($creatPremium){
                    return response()->json(['success_percentage' => 'percentage_applied']);
                }else{
                    return response()->json(['error_apply_percentage' => 'error_applying_percentage']);
                }
            }else{
                return response()->json(['premium_exists' => 'premium_exists']);
            }

        }
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
            $from_time = Utilities::removeSpace($request->from_time[$i]);
            $to_time = Utilities::removeSpace($request->to_time[$i]);
            $adslot_array[] = [
                'id'=> uniqid(),
                'rate_card' => $rate_card_id,
                'target_audience' => $request->target_audiences[$i],
                'day_parts' => $request->dayparts[$i],
                'region' => $request->regions[$i],
                'from_to_time' => $from_time. ' - '.$to_time,
                'min_age' => $request->min_age[$i],
                'max_age' => $request->max_age[$i],
                'broadcaster' => $broadcaster_id,
                'is_available' => 0,
                'time_difference' => strtotime($from_time) - strtotime($to_time),
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

    public function premiumPrices($selectAdslotPrice, $request, $adslot)
    {
        $premium_60 = ($selectAdslotPrice[0]->price_60 + (((int)$request->premium_percent) / 100) * $selectAdslotPrice[0]->price_60);
        $premium_45 = ($selectAdslotPrice[0]->price_45 + (((int)$request->premium_percent) / 100) * $selectAdslotPrice[0]->price_45);
        $premium_30 = ($selectAdslotPrice[0]->price_30 + (((int)$request->premium_percent) / 100) * $selectAdslotPrice[0]->price_30);
        $premium_15 = ($selectAdslotPrice[0]->price_15 + (((int)$request->premium_percent) / 100) * $selectAdslotPrice[0]->price_15);

        $premium = [
            'id' => uniqid(),
            'adslot_id' => $adslot,
            'price_60' => $premium_60,
            'price_45' => $premium_45,
            'price_30' => $premium_30,
            'price_15' => $premium_15,
            'percentage' => $request->premium_percent
        ];

        return $premium;

    }

    public function getAdslotPercentages($adslot_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from adslotPercentages where adslot_id = '$adslot_id'");
    }


}
