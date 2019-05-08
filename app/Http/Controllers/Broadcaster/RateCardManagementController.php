<?php

namespace Vanguard\Http\Controllers\Broadcaster;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\StoreRateCardRequest;
use Vanguard\Models\Ratecard\Ratecard;
use Vanguard\Services\Company\CompanyDetailsFromIdList;
use Vanguard\Services\RateCard\CheckRateCardExistence;
use Vanguard\Services\RateCard\GetRateCardById;
use Vanguard\Services\RateCard\GetRateCardForStation;
use Vanguard\Services\RateCard\StoreBaseRateCard;
use Vanguard\Services\RateCard\UpdateRateCardService;
use Yajra\DataTables\DataTables;

class RateCardManagementController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware(['role:ssp.super_admin|ssp.admin']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $get_rate_card_service = new GetRateCardForStation($this->getCompanyIdsList());
        return view('broadcaster_module.rate-card-management.index')->with('rate_cards', $get_rate_card_service->formatRateCardData());
    }

    public function formatToDataTable(DataTables $dataTables)
    {
        $get_rate_card_service = new GetRateCardForStation($this->getCompanyIdsList());
        $rate_cards = $get_rate_card_service->formatRateCardData();
        return $dataTables->collection($rate_cards)
            ->addColumn('edit', function ($rate_cards) {
                return '<a href="'.route('rate_card.management.edit', ['rate_card_id' => $rate_cards['id']]).'" class="weight_medium">Edit</a>';
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
        $company_ids = $this->getCompanyIdsList();
        $companies_service = new CompanyDetailsFromIdList($company_ids);
        $rate_cards = Ratecard::whereIn('company_id', $company_ids);
        return view('broadcaster_module.rate-card-management.create')
                                                    ->with('companies', $companies_service->getCompanyDetails())
                                                    ->with('rate_card_count', $rate_cards->count())
                                                    ->with('company_id_count', count($company_ids));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRateCardRequest $request)
    {
        $store_rate_card_service = new StoreBaseRateCard($this->requestData($request));
        $store_rate_card = $store_rate_card_service->run();
        if($store_rate_card['status'] == 'success'){
            \Session::flash('success', 'Rate Card created successfully');
            return redirect()->route('rate_card.management.index');
        }else{
            \Session::flash('error', 'An error occurred while creating your rate card');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $get_rate_card_by_id_service = new GetRateCardById($id);
        $rate_card = $get_rate_card_by_id_service->getRateCardById();
        if(!$rate_card){
            \Session::flash('error', 'Could not fetch your rate card details');
            return redirect()->back();
        }
        return view('broadcaster_module.rate-card-management.edit')->with('rate_card', $rate_card);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRateCardRequest $request, $id)
    {
        $update_rate_card_service = new UpdateRateCardService($id, $this->requestData($request));
        $update_rate_card = $update_rate_card_service->run();
        if($update_rate_card['status'] == 'success'){
            \Session::flash('success', 'Rate card updated successfully');
            return redirect()->route('rate_card.management.index');
        }else{
            \Session::flash('error', 'An error occurred while updating your rate card');
            return redirect()->back();
        }

    }

    public function requestData($request)
    {
        return [
            'duration' => $request->duration,
            'price' => $request->price,
            'name' => $request->title,
            'company_id' => $request->company,
            'is_base' => $request->is_base ? $request->is_base : false
        ];
    }

}
