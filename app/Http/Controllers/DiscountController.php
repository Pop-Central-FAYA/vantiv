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

        $agencies_user_ids = Utilities::switch_db('reports')->select("SELECT user_id FROM agents");

        $agencies = [];

        foreach ($agencies_user_ids as $agency) {

            $user_id = $agency->user_id;

            $fullname = Api::get_agent_user($user_id);

            $agencies[] = [
                'id' => $fullname[0]->id,
                'fullname' => $fullname[0]->firstname . ' ' . $fullname[0]->lastname
            ];
        }

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

    public function searchObject($categories, $id, $index)
    {
        foreach ($categories as $category) {
            if ($id == $category->id) {
                return $category->$index;
            }
        }

        return false;
    }

    public function store(Request $request)
    {
        $hourly_ranges = Api::get_hourly_ranges();
        $day_parts = Api::get_dayParts();
        $types = Api::get_discountTypes();
        $agencies = Api::get_agencies();
        $classes = Api::get_discount_classes();

        $number_value = (int) $request->value;
        $percent_value = (int) $request->percent_value;

        $broadcaster_id = \Session::get('broadcaster_id');

        if ($number_value !== 0 && $percent_value !== 0) {
            $request->discount_class_id = $classes[2]->id;
        } elseif ($percent_value !== 0 && $number_value == 0) {
            $request->discount_class_id = $classes[0]->id;
        } elseif ($number_value !== 0 && $percent_value == 0) {
            $request->discount_class_id = $classes[1]->id;
        } else {
            $request->discount_class_id = null;
        }

        $discount_type_id = $request->discount_type_id;
        $discount_type_value = $request->discount_type_value;

        if ($discount_type_id == $types[0]->id) {
            $discount_type_sub_value = $this->searchObject($agencies, $discount_type_value, 'brand');
        } elseif ($discount_type_id == $types[1]->id) {
            $discount_type_sub_value = null;
        } elseif ($discount_type_id == $types[2]->id) {
            $discount_type_sub_value = $this->searchObject($hourly_ranges, $discount_type_value, 'time_range');
        } elseif ($discount_type_id == $types[3]->id) {
            $discount_type_sub_value = $this->searchObject($day_parts, $discount_type_value, 'day_parts');
        } elseif ($discount_type_id == $types[4]->id || $discount_type_id == $types[5]->id) {
            $discount_type_sub_value = $request->discount_type_sub_value;
        } else {
            $discount_type_sub_value = null;
        }

        $discountInsert = Utilities::switch_db('reports')->table('discounts')->insert([
            'id' => uniqid(),
            'broadcaster' => $broadcaster_id,
            'discount_type' => $request->discount_type_id,
            'discount_class' => $request->discount_class_id,
            'discount_type_value' => $request->discount_type_value,
            'percent_value' => (int) $request->percent_value,
            'percent_start_date' => $request->percent_start_date,
            'percent_stop_date' => $request->percent_stop_date,
            'value' => (int) $request->value,
            'value_start_date' => $request->value_start_date,
            'value_stop_date' => $request->value_stop_date,
            'discount_type_sub_value' => $discount_type_sub_value
        ]);

        if($discountInsert) {
            return redirect()->back()->with('success', 'Discount created successfully');
        } else {
            return redirect()->back()->with('error', 'Discount not created');
        }
    }

    public function update(Request $request, $discount)
    {
        $number_value = (int) $request->value;
        $percent_value = (int) $request->percent_value;

        $hourly_ranges = Api::get_hourly_ranges();
        $day_parts = Api::get_dayParts();
        $types = Api::get_discountTypes();
        $agencies = Api::get_agencies();
        $classes = Api::get_discount_classes();

        $broadcaster_id = \Session::get('broadcaster_id');

        if ($number_value !== 0 && $percent_value !== 0) {
            $request->discount_class_id = $classes[2]->id;
        } elseif ($percent_value !== 0 && $number_value == 0) {
            $request->discount_class_id = $classes[0]->id;
        } elseif ($number_value !== 0 && $percent_value == 0) {
            $request->discount_class_id = $classes[1]->id;
        } else {
            $request->discount_class_id = null;
        }

        $discount_type_id = $request->discount_type_id;
        $discount_type_value = $request->discount_type_value;

        if ($discount_type_id == $types[0]->id) {
            $discount_type_sub_value = $this->searchObject($agencies, $discount_type_value, 'brand');
        } elseif ($discount_type_id == $types[1]->id) {
            $discount_type_sub_value = null;
        } elseif ($discount_type_id == $types[2]->id) {
            $discount_type_sub_value = $this->searchObject($hourly_ranges, $discount_type_value, 'time_range');
        } elseif ($discount_type_id == $types[3]->id) {
            $discount_type_sub_value = $this->searchObject($day_parts, $discount_type_value, 'day_parts');
        } elseif ($discount_type_id == $types[4]->id || $discount_type_id == $types[5]->id) {
            $discount_type_sub_value = $request->discount_type_sub_value;
        } else {
            $discount_type_sub_value = null;
        }

        $discountUpdate = Utilities::switch_db('reports')
            ->select(
                "UPDATE discounts 
                  SET
                  broadcaster = '$broadcaster_id',
                  discount_type = '$request->discount_type_id',
                  discount_class = '$request->discount_class_id',
                  discount_type_value = '$request->discount_type_value',
                  percent_value = '$request->percent_value',
                  percent_start_date = '$request->percent_start_date',
                  percent_stop_date = '$request->percent_stop_date',
                  value =  '$request->value',
                  value_start_date = '$request->value_start_date',
                  value_stop_date = '$request->value_stop_date',
                  discount_type_sub_value = '$discount_type_sub_value'
                  WHERE id = '$discount'"
            );

        if(empty($discountUpdate)) {
            return redirect()->back()->with('success', 'Discount updated successfully');
        } else {
            return redirect()->back()->with('error', 'Discount not updated, try again');
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

}