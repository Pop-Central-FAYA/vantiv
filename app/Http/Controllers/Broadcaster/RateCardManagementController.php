<?php

namespace Vanguard\Http\Controllers\Broadcaster;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\StoreRateCardRequest;
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $get_rate_card_service = new GetRateCardForStation($this->companyId());
        return view('broadcaster_module.rate-card-management.index')->with('rate_cards', $get_rate_card_service->formatRateCardData());
    }

    public function formatToDataTable(DataTables $dataTables)
    {
        $get_rate_card_service = new GetRateCardForStation($this->companyId());
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
        $companies_service = new CompanyDetailsFromIdList($this->companyId());
        if(is_array($this->companyId())){
            $companies = $companies_service->getCompanyDetails();
        }else{
            $companies = '';
        }
        return view('broadcaster_module.rate-card-management.create')->with('companies', $companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRateCardRequest $request)
    {
        //check rate card existence
        if(is_array($this->companyId())){
            $company_id = $request->company;
        }else{
            $company_id = $this->companyId();
        }
        $check_rate_card_existence_service = new CheckRateCardExistence($request->name, $company_id);
        if($check_rate_card_existence_service->checkRateCardExistence()){
            \Session::flash('info', 'Rate Card with this name already exist');
            return redirect()->back();
        }
        //save if not exist
        $store_rate_card_service = new StoreBaseRateCard($company_id, $request->duration, $request->price, $request->name);
        $store_rate_card = $store_rate_card_service->storeRateCardWithDurationAndPrice();

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
        //update rate card
        if(is_array($this->companyId())){
            $company_id = $request->company;
        }else{
            $company_id = $this->companyId();
        }
        $update_rate_card_service = new UpdateRateCardService($company_id, $request->duration, $request->price, $request->name, $id);
        $update_rate_card = $update_rate_card_service->updateRateCardWithPrice();
        if($update_rate_card['status'] == 'success'){
            \Session::flash('success', 'Rate card updated successfully');
            return redirect()->route('rate_card.management.index');
        }else{
            \Session::flash('error', 'An error occurred while updating your rate card');
            return redirect()->back();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
