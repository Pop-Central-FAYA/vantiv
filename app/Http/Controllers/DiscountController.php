<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Api;
use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;

class DiscountController extends Controller
{
    public function index()
    {
        $hourly_ranges = Api::get_hourly_ranges();
        $day_parts = Api::get_dayParts();
        $types = Api::get_discountTypes();

        $agencies = Utilities::switch_db('reports')->select("SELECT u.id as user_id, CONCAT(u.firstname,' ', u.lastname) as name FROM agents as a INNER JOIN users as u ON u.id = a.user_id");

        $brands = Utilities::switch_db('reports')->select("SELECT id, name FROM brands");

        $agency_discounts = Api::get_discounts_by_type($types[0]->id);
        $brand_discounts = Api::get_discounts_by_type($types[1]->id);
        $time_discounts = Api::get_discounts_by_type($types[2]->id);
        $daypart_discounts = Api::get_discounts_by_type($types[3]->id);
        $price_discounts = Api::get_discounts_by_type($types[4]->id);
        $pslot_discounts = Api::get_discounts_by_type($types[5]->id);


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

        $discount_class_type = $this->getDiscountAndClass($request);

        dd($discount_class_type);

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
        $discount_class_type = $this->getDiscountAndClass($request);

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
            \Session::flash('error', 'Discount not updated, try again');
            return redirect()->back();
        }
    }

    public function destroy($discount)
    {
        $delete_discount = Utilities::switch_db('reports')->select("UPDATE discounts SET status = '0' WHERE id = '$discount' AND status = '0'");

        dd($delete_discount);

        if(empty($delete_discount)) {
            return redirect()->back()->with('success', 'Discount deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Discount not deleted, try again');
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

    public function getDiscountTypeSubValue($request, $agencies, $types, $hourly_ranges, $day_parts )
    {
        $discount_type_id = $request->discount_type_id;
        $discount_type_value = $request->discount_type_value;

        switch ($discount_type_value){
            case $discount_type_id == $types[0]->id:
                $discount_type_sub_value = $this->searchObject($agencies, $discount_type_value, 'brand');
                break;
            case $discount_type_id == $types[1]->id:
                $discount_type_sub_value = null;
                break;
            case $discount_type_id == $types[2]->id:
                $discount_type_sub_value = $this->searchObject($hourly_ranges, $discount_type_value, 'time_range');
                break;
            case $discount_type_id == $types[3]->id:
                $discount_type_sub_value = $this->searchObject($day_parts, $discount_type_value, 'day_parts');
                break;
            case $discount_type_id == $types[4]->id || $discount_type_id == $types[5]->id:
                $discount_type_sub_value = $request->discount_type_sub_value;
                break;
            default :
                $discount_type_sub_value = null;
        }

        return $discount_type_sub_value;
    }

    public function getDiscountAndClass($request)
    {
        $hourly_ranges = Api::get_hourly_ranges();
        $day_parts = Api::get_dayParts();
        $types = Api::get_discountTypes();
        $agencies = Api::get_agencies();
        $classes = Api::get_discount_classes();
        $number_value = (int) $request->value;
        $percent_value = (int) $request->percent_value;

        $discount_class_id = $this->getDiscountClassId($number_value, $percent_value, $classes);

        $discount_type_sub_value = $this->getDiscountTypeSubValue($request, $agencies, $types, $hourly_ranges, $day_parts);

        return json_encode(['discount_class_id' => $discount_class_id, 'discount_type_sub_value' => $discount_type_sub_value]);
    }

    public function searchObject($categories, $id, $index)
    {
        foreach ($categories as $category) {
            if ($id == $category->id) {
                return $category->$index;
            }
        }

        return false;
    }

}