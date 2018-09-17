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

        $agency_discounts = Api::get_agency_discounts($types['agency'], $broadcaster_id);
        $brand_discounts = Api::get_brand_discounts($types['brands'], $broadcaster_id);
        $time_discounts = Api::get_time_discounts($types['time'], $broadcaster_id);
        $daypart_discounts = Api::get_dayparts_discount($types['day_parts'], $broadcaster_id);
        $price_discounts = Api::getPriceDiscount($types['price'], $broadcaster_id);


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
//        dd($request->all());

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
            'discount_type_sub_value' => $request->discount_type_value
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
                  discount_type_sub_value = '$request->discount_type_value'
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
            return $classes['both'];
        }elseif ($percent_value !== 0 && $number_value == 0){
            return $classes['percent'];
        }else{
            return $classes['number']->id;
        }

    }


    public function getDiscountAndClass($request)
    {

        $types = Api::get_discountTypes();
        $classes = Api::get_discount_classes();
        $number_value = (int) $request->value;
        $percent_value = (int) $request->percent_value;

        $discount_class_id = $this->getDiscountClassId($number_value, $percent_value, $classes);

        if($types['price'] === $request->discount_type_id){
            if((integer)$request->discount_type_sub_value < (integer)$request->discount_type_value){
                return 'error';
            }
        }

        return (object)(['discount_class_id' => $discount_class_id]);
    }


}