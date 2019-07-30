<?php

namespace Vanguard\Http\Controllers\Ssp;

use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\DiscountStoreRequest;
use Illuminate\Http\Request;
use Vanguard\Services\Company\CompanyDetailsFromIdList;
use Vanguard\Services\Discount\GetDiscountById;
use Vanguard\Services\Discount\GetPublisherDiscountList;
use Vanguard\Services\Discount\StoreDiscount;
use Vanguard\Services\Discount\UpdateDiscount;
use Vanguard\Http\Controllers\Controller;

use Yajra\DataTables\DataTables;

class DiscountController extends Controller
{

    use CompanyIdTrait;

    public function index()
    {
        return view('broadcaster_module.discounts.index');
    }

    public function dataTable(DataTables $dataTables)
    {
        $get_discount_list_service = new GetPublisherDiscountList($this->companyId());
        $discounts = $get_discount_list_service->getDiscountList();
        return $dataTables->collection($discounts)
            ->addColumn('station', function ($discounts) {
                return $discounts->company->name;
            })
            ->addColumn('edit', function ($discounts) {
                if(\Auth::user()->hasPermissionTo('update.discount')){
                    return '<a href="'.route('discount.edit', ['id' => $discounts->id]).'" class="weight_medium">Edit</a>';
                }
            })
            ->rawColumns(['edit' => 'edit', 'station' => 'station'])->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        $companies_service = new CompanyDetailsFromIdList($this->companyId());
        if(is_array($this->companyId())){
            $companies = $companies_service->getCompanyDetails();
        }else{
            $companies = '';
        }
        return view('broadcaster_module.discounts.create')->with('companies', $companies);
    }

    public function store(DiscountStoreRequest $request)
    {
        if(is_array($this->companyId())){
            $company_id = $request->company;
        }else{
            $company_id = $this->companyId();
        }
        try{
            $store_discount_service = new StoreDiscount($request->name, $request->percentage, $company_id);
            $store_discount_service->storeDiscount();
        }catch (\Exception $exception){
            \Session::flash('error', 'An error occurred while performing your request');
            return redirect()->back();
        }
        \Session::flash('success', 'Discount created successfully');
        return redirect()->route('discount.index');
    }

    public function edit($id)
    {
        $get_discount_service = new GetDiscountById($id);
        return view('broadcaster_module.discounts.edit')->with('discount', $get_discount_service->getDiscount());
    }

    public function update(Request $request, $id)
    {
        if(is_array($this->companyId())){
            $company_id = $request->company;
        }else{
            $company_id = $this->companyId();
        }
        try{
            $update_discount_service = new UpdateDiscount($request->name, $request->percentage, $company_id, $id);
            $update_discount_service->updateDiscount();
        }catch (\Exception $exception){
            \Session::flash('error', 'An error occurred while performing your request');
            return redirect()->back();
        }
        \Session::flash('success', 'Discount updated successfully');
        return redirect()->route('discount.index');
    }

}
