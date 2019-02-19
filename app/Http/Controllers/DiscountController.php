<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Libraries\Api;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Vanguard\Services\Brands\CompanyBrands;
use Vanguard\Services\Company\CompanyList;
use Vanguard\Services\Discount\BrandDiscountList;
use Vanguard\Services\Discount\CompanyDiscountList;
use Vanguard\Services\Discount\DayPartDiscountList;
use Vanguard\Services\Discount\PriceDiscountList;
use Vanguard\Services\Discount\TimeDiscountList;
use Vanguard\Services\PreloadedData\PreloadedData;

class DiscountController extends Controller
{

    use CompanyIdTrait;

    public function index()
    {
        $preloaded_data_service = new PreloadedData();
        $types = $preloaded_data_service->getDiscountTypes();
        $agencies_service = new CompanyList($this->companyId());
        $brand_service = new CompanyBrands($this->companyId());

        $discount_company_list_service = new CompanyDiscountList($this->companyId(), $types['Agency']);
        $discount_brand_list_service = new BrandDiscountList($this->companyId(), $types['Brands']);
        $discount_time_list_service = new TimeDiscountList($this->companyId(), $types['Time']);
        $discount_dayparts_service = new DayPartDiscountList($this->companyId(), $types['Day Part']);
        $discount_price_service = new PriceDiscountList($this->companyId(), $types['Price']);

        return view('broadcaster_module.discounts.index')
                        ->with('agency_discounts', $discount_company_list_service->getDiscountOfCompanyTypeAgency())
                        ->with('brand_discounts', $discount_brand_list_service->getBrandDiscountList())
                        ->with('time_discounts', $discount_time_list_service->getTimeDiscount())
                        ->with('daypart_discounts', $discount_dayparts_service->getDayPartDiscountList())
                        ->with('price_discounts', $discount_price_service->getPriceDiscountList())
                        ->with('hourly_ranges', $preloaded_data_service->getHourlyRanges())
                        ->with('day_parts', $preloaded_data_service->getDayParts())
                        ->with('types', $preloaded_data_service->getDiscountTypes())
                        ->with('agencies', $agencies_service->getCompanyListWithTypeAgency())
                        ->with('brands', $brand_service->getBrandCreatedByCompany());

    }

    public function store(Request $request)
    {

        $broadcaster_id = \Session::get('broadcaster_id');

        $discount_class_type = $this->getDiscountAndClass($request, $broadcaster_id);

        if($discount_class_type === 'error'){
            \Session::flash('error', 'Please ensure that the Max. Value is greater than the Min. value');
            return redirect()->back();
        }

        $discountInsert = Utilities::switch_db('reports')->table('discounts')->insert([
            'id' => uniqid(),
            'broadcaster' => $broadcaster_id,
            'discount_type' => $request->discount_type_id,
            'discount_class' => $discount_class_type->discount_class_id,
            'discount_type_value' => $request->discount_type_value,
            'percent_value' => (int) $request->percent_value,
            'percent_start_date' => $request->percent_start_date,
            'percent_stop_date' => $request->percent_stop_date,
            'value' => (int) $request->value,
            'value_start_date' => $request->value_start_date,
            'value_stop_date' => $request->value_stop_date,
            'discount_type_sub_value' => $request->discount_type_sub_value ? $request->discount_type_sub_value : $request->discount_type_value
        ]);

        if($discountInsert) {
            \Session::flash('success', 'Discount created successfully');
            return redirect()->back();
        } else {
            \Session::flash('success', 'Discount not created');
            return redirect()->back();
        }
    }

    public function update(Request $request, $discount)
    {

        $broadcaster_id = \Session::get('broadcaster_id');
        $discount_class_type = $this->getDiscountAndClass($request, $broadcaster_id);

        if($discount_class_type === 'error'){
            \Session::flash('error', 'Please ensure that the Max. Value is greater than the Min. value');
            return redirect()->back();
        }

        if($request->discount_type_sub_value){
            $discount_type_sub_value = $request->discount_type_sub_value;
        }else{
            $discount_type_sub_value = $request->discount_type_value;
        }

        $discountUpdate = Utilities::switch_db('reports')
            ->update(
                "UPDATE discounts 
                  SET
                  broadcaster = '$broadcaster_id',
                  discount_type = '$request->discount_type_id',
                  discount_class = '$discount_class_type->discount_class_id',
                  discount_type_value = '$request->discount_type_value',
                  percent_value = '$request->percent_value',
                  percent_start_date = '$request->percent_start_date',
                  percent_stop_date = '$request->percent_stop_date',
                  `value` =  '$request->value',
                  value_start_date = '$request->value_start_date',
                  value_stop_date = '$request->value_stop_date',
                  discount_type_sub_value = '$discount_type_sub_value'
                  WHERE id = '$discount'"
            );

        if($discountUpdate) {
            \Session::flash('success', 'Discount updated successfully');
            return redirect()->back();
        } else {
            \Session::flash('error', 'Discount not updated or no changes made, try again');
            return redirect()->back();
        }
    }

    public function destroy($discount)
    {
        $delete_discount = Utilities::switch_db('api')->update("UPDATE discounts SET status = 0 WHERE id = '$discount' AND status = 1");

        if($delete_discount) {
            \Session::flash('success', 'Discount deleted successfully');
            return redirect()->back();
        } else {
            \Session::flash('error', 'Discount not deleted, try again');
            return redirect()->back();
        }

    }

    public function getDiscountClassId($number_value, $percent_value, $classes)
    {
        if($number_value !== 0 && $percent_value !== 0){
            return $classes['Both'];
        }elseif ($percent_value !== 0 && $number_value == 0){
            return $classes['Percent'];
        }else{
            return $classes['Number']->id;
        }

    }


    public function getDiscountAndClass($request)
    {
        $types = Api::get_discountTypes();
        $classes = Api::get_discount_classes();
        $number_value = (int) $request->value;
        $percent_value = (int) $request->percent_value;

        $discount_class_id = $this->getDiscountClassId($number_value, $percent_value, $classes);

        if($types['Price'] === $request->discount_type_id){
            if((integer)$request->discount_type_sub_value < (integer)$request->discount_type_value){
                return 'error';
            }
        }

        return (object)(['discount_class_id' => $discount_class_id]);
    }


}
