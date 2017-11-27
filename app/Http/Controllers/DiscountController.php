<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Api;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $agency_discounts = json_decode(Api::get_discounts_by_type(1))->data;
        $brand_discounts = json_decode(Api::get_discounts_by_type(2))->data;
        $time_discounts = json_decode(Api::get_discounts_by_type(3))->data;
        $daypart_discounts = json_decode(Api::get_discounts_by_type(4))->data;
        $price_discounts = json_decode(Api::get_discounts_by_type(5))->data;
        $pslot_discounts = json_decode(Api::get_discounts_by_type(6))->data;

        $hourly_ranges = json_decode(Api::get_hourly_range())->data;
        $day_parts = json_decode(Api::get_dayParts())->data;

        return view('discounts.index',
            compact(
                'agency_discounts', 'brand_discounts', 'time_discounts',
                'daypart_discounts', 'price_discounts', 'pslot_discounts',
                'hourly_ranges','day_parts'
            )
        );
    }

    public function store(Request $request)
    {
        $number_value = (int) $request->discount_value_number;
        $percent_value = (int) $request->discount_value_percent;

        if ($number_value !== 0 && $percent_value !== 0) {
            $request->discount_type = 3;
        } elseif ($percent_value !== 0 && $number_value == 0) {
            $request->discount_type = 1;
        } elseif ($number_value !== 0 && $percent_value == 0) {
            $request->discount_type = 2;
        } else {
            $request->discount_type = null;
        }

        $type = $request->type;
        $discount_type = $request->discount_type;
        $discount_value_number = $request->discount_value_number;
        $discount_value_percent = $request->discount_value_percent;
        $type_value = $request->type_value;
        $price_range_from = $request->price_range_from;
        $price_range_to = $request->price_range_to;
        $price_slot_from = $request->price_slot_from;
        $price_slot_to = $request->price_slot_to;

        $discount = json_decode(Api::create_discount(
            $type, $discount_type, $discount_value_number,
            $discount_value_percent, $type_value, $price_range_from,
            $price_range_to, $price_slot_from, $price_slot_to
        ));

        if($discount->status === false) {
            return redirect()->back()->with('error', $discount->message);
        } else {
            return redirect()->back()->with('success', trans('app.discount_created'));
        }
    }

    public function update(Request $request, $discount)
    {
        $number_value = (int) $request->discount_value_number;
        $percent_value = (int) $request->discount_value_percent;

        if ($number_value !== 0 && $percent_value !== 0) {
            $request->discount_type = 3;
        } elseif ($percent_value !== 0 && $number_value == 0) {
            $request->discount_type = 1;
        } elseif ($number_value !== 0 && $percent_value == 0) {
            $request->discount_type = 2;
        } else {
            $request->discount_type = null;
        }

        $type = (int) $request->type;
        $discount_type = $request->discount_type;
        $discount_value_number = $number_value;
        $discount_value_percent = $percent_value;
        $type_value = $request->type_value;
        $price_range_from = $request->price_range_from;
        $price_range_to = $request->price_range_to;
        $price_slot_from = $request->price_slot_from;
        $price_slot_to = $request->price_slot_to;

        $discount = json_decode(Api::update_discount(
            $discount, $type, $discount_type,
            $discount_value_number, $discount_value_percent, $type_value,
            $price_range_from,
            $price_range_to, $price_slot_from, $price_slot_to
        ));

        if($discount->status === false) {
            return redirect()->back()->with('error', $discount->message);
        } else {
            return redirect()->back()->with('success', trans('app.discount_updated'));
        }
    }

    public function destroy($discount)
    {
        $delete_discount = json_decode(Api::delete_discount($discount));

        if($delete_discount->status === false) {
            return redirect()->back()->with('error', $delete_discount->message);
        } else {
            return redirect()->back()->with('success', trans('app.discount_deleted'));
        }
    }

}
