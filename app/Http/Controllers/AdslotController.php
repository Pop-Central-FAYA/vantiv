<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Vanguard\Http\Requests\StoreAdslotsRequests;
use Vanguard\Http\Requests\UpdateAdslotsRequest;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Maths;
use League\Flysystem\Exception;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\Adslot\Adslotlist;
use Vanguard\Services\Adslot\StoreAdslot;
use Vanguard\Services\Adslot\StoreAdslotPrice;
use Vanguard\Services\Company\CompanyDetails;
use Vanguard\Services\PreloadedData\PreloadedData;
use Vanguard\Services\RateCard\RatecardExistenceCheck;
use Vanguard\Services\RateCard\StoreRateCard;
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
        $preloaded_data = new PreloadedData();
        return view('broadcaster_module.adslots.create')->with('days', $preloaded_data->getDays())
                                                            ->with('hours', $preloaded_data->getHourlyRanges())
                                                            ->with('regions', $preloaded_data->getRegions())
                                                            ->with('target_audiences', $preloaded_data->getTargetAudiences())
                                                            ->with('channels', $preloaded_data->getCampaignChannels())
                                                            ->with('day_parts', $preloaded_data->getDayParts());
    }

    public function store(StoreAdslotsRequests $request)
    {
        $company_id = \Auth::user()->companies->first()->id;
        $company_details_service = new CompanyDetails($company_id);
        $company_details = $company_details_service->getCompanyDetails();
        $user_id = \Auth::user()->id;
        $time_sum = $this->checkTotalTime($request->from_time, $request->to_time);
        if($time_sum > 720){
            Session::flash('error', ClassMessages::ADSLOT_TIME_CHECK);
            return redirect()->back();
        }
        $rate_cared_existence_service = new RatecardExistenceCheck($company_id, $request->hourly_ranges, $request->days);
        if($rate_cared_existence_service->checkIfRatecardExists()){
            Session::flash('error', ClassMessages::RATECARD_EXISTENCE);
            return redirect()->back();
        }

        try{
            \DB::transaction(function() use ($user_id, $company_id, $request, $company_details) {
                $rate_card_store_service = new StoreRateCard($user_id, $company_id, $request->days, $request->hourly_ranges);
                $rate_card = $rate_card_store_service->storeRateCard();
                for ($i = 0; $i < count($request->regions); $i++){
                    $from_time = Utilities::removeSpace($request->from_time[$i]);
                    $to_time = Utilities::removeSpace($request->to_time[$i]);
                    $adslot_store_service = new StoreAdslot($rate_card->id,$request->target_audiences[$i],$request->dayparts[$i],
                        $request->regions[$i],$from_time. ' - '.$to_time, $request->min_age[$i], $request->max_age[$i],
                        $company_id, $this->timeDifference($from_time, $to_time),$company_details->channels->first()->id);
                    $adslot = $adslot_store_service->storeAdslot();
                    $store_adslot_price_service = new StoreAdslotPrice($adslot->id, $request->price_60[$i], $request->price_45[$i],
                        $request->price_30[$i], $request->price_15[$i]);
                    $store_adslot_price_service->storeAdslotPrice();
                }
            });
        }catch (\Exception $exception){
            Session::flash('error', ClassMessages::ADSLOT_ERROR);
            return redirect()->back();
        }
        Session::flash('success', ClassMessages::ADSLOT_SUCCESS);
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

    public function checkTotalTime($from_time, $to_time)
    {
        $time_sum = 0;
        for($x = 0; $x < count($from_time); $x++){
            $time_difference = (strtotime(Utilities::removeSpace($to_time[$x])) - strtotime(Utilities::removeSpace($from_time[$x])));
            $time_sum += $time_difference;
        }
        return $time_sum;
    }

    public function timeDifference($from_time, $to_time)
    {
        return (strtotime($to_time)-strtotime($from_time));
    }


}
