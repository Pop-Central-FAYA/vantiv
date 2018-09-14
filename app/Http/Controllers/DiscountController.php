<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Api;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;

class DiscountController extends Controller
{
    public function index()
    {
        $broadcaster_id = \Session::get('broadcaster_id');
        $hourly_ranges = Api::get_hourly_ranges();
        $day_parts = Api::get_dayParts();
        $types = Api::get_discountTypes();

        $agencies = Utilities::switch_db('reports')->select("SELECT u.id as user_id, a.id as agency_id, CONCAT(u.firstname,' ', u.lastname) as name FROM agents as a INNER JOIN users as u ON u.id = a.user_id");

        $brands = Api::get_brands($broadcaster_id);

        $agency_discounts = Api::get_agency_discounts($types[0]->id, $broadcaster_id);
        $brand_discounts = Api::get_brand_discounts($types[1]->id, $broadcaster_id);
        $time_discounts = Api::get_time_discounts($types[2]->id, $broadcaster_id);
        $daypart_discounts = Api::get_dayparts_discount($types[3]->id, $broadcaster_id);
        $price_discounts = Api::getPriceDiscount($types[4]->id, $broadcaster_id);
//        $pslot_discounts = Api::get_discounts_by_type($types[5]->id);


        return view('broadcaster_module.discounts.index',
            compact(
                'agency_discounts', 'brand_discounts', 'time_discounts',
                'daypart_discounts', 'price_discounts', 'pslot_discounts',
                'hourly_ranges','day_parts', 'types', 'agencies', 'brands'
            )
        );
    }

    public function store(Request $request)
    {

        $broadcaster_id = \Session::get('broadcaster_id');

        $discount_class_type = $this->getDiscountAndClass($request, $broadcaster_id);

        if($discount_class_type->discount_type_sub_value === 'error'){
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
            'discount_type_sub_value' => $discount_class_type->discount_type_sub_value
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

        if($discount_class_type->discount_type_sub_value === 'error'){
            \Session::flash('error', 'Please ensure that the Max. Value is greater than the Min. value');
            return redirect()->back();
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
                  discount_type_sub_value = '$discount_class_type->discount_type_sub_value'
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
        dd($discount);
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
            return $classes[2]->id;
        }elseif ($percent_value !== 0 && $number_value == 0){
            return $classes[2]->id;
        }elseif ($number_value !== 0 && $percent_value == 0){
            return $classes[1]->id;
        }else{
            return null;
        }

    }

    public function getDiscountTypeSubValue($request, $agencies, $types,$brands, $hourly_ranges, $day_parts )
    {
        $discount_type_id = $request->discount_type_id;
        $discount_type_value = $request->discount_type_value;
        if($discount_type_id == $types[0]->id){
            return $this->searchObject($agencies, $discount_type_value);
        }elseif ($discount_type_id == $types[1]->id){
            return $this->searchObject($brands, $discount_type_value);
        }elseif ($discount_type_id == $types[2]->id){
            return $this->searchObject($hourly_ranges, $discount_type_value);
        }elseif ($discount_type_id == $types[3]->id){
            return $this->searchObject($day_parts, $discount_type_value);
        }elseif ($discount_type_id == $types[4]->id ){
            if((integer)$request->discount_type_sub_value < $request->discount_type_value){
                return 'error';
            }else{
                return $request->discount_type_sub_value;
            }
        }else{
            return null;
        }
    }

    public function getDiscountAndClass($request, $broadcaster_id)
    {
        $hourly_ranges = Api::get_hourly_ranges();
        $day_parts = Api::get_dayParts();
        $types = Api::get_discountTypes();
        $agencies = Api::get_agencies();
        $classes = Api::get_discount_classes();
        $brands = Api::get_brands($broadcaster_id);
        $number_value = (int) $request->value;
        $percent_value = (int) $request->percent_value;

        $discount_class_id = $this->getDiscountClassId($number_value, $percent_value, $classes);

        $discount_type_sub_value = $this->getDiscountTypeSubValue($request, $agencies, $types, $brands, $hourly_ranges, $day_parts);



        return (object)(['discount_class_id' => $discount_class_id, 'discount_type_sub_value' => $discount_type_sub_value]);
    }

    public function searchObject($categories, $id)
    {
        foreach ($categories as $category) {
            if ($id == $category->id) {
                return $category->id;
            }
        }

        return false;
    }

}