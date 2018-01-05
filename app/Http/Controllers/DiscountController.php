<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\Api;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $hourly_ranges = Api::get_ratecard_preloaded()->data->hourly_range;
        $day_parts = Api::get_ratecard_preloaded()->data->day_parts;
        $types = Api::get_ratecard_preloaded()->data->discount_types;
        $agencies = Api::get_ratecard_preloaded()->data->agency;

        $agency_discounts = json_decode(Api::get_discounts_by_type($types[0]->id))->data;
        $brand_discounts = json_decode(Api::get_discounts_by_type($types[1]->id))->data;
        $time_discounts = json_decode(Api::get_discounts_by_type($types[2]->id))->data;
        $daypart_discounts = json_decode(Api::get_discounts_by_type($types[3]->id))->data;
        $price_discounts = json_decode(Api::get_discounts_by_type($types[4]->id))->data;
        $pslot_discounts = json_decode(Api::get_discounts_by_type($types[5]->id))->data;

        return view('discounts.index',
            compact(
                'agency_discounts', 'brand_discounts', 'time_discounts',
                'daypart_discounts', 'price_discounts', 'pslot_discounts',
                'hourly_ranges','day_parts', 'types', 'agencies'
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
        $hourly_ranges = Api::get_ratecard_preloaded()->data->hourly_range;
        $day_parts = Api::get_ratecard_preloaded()->data->day_parts;
        $types = Api::get_ratecard_preloaded()->data->discount_types;
        $agencies = Api::get_ratecard_preloaded()->data->agency;

        $number_value = (int) $request->value;
        $percent_value = (int) $request->percent_value;

        $classes = Api::get_ratecard_preloaded()->data->discount_classes;
        $types = Api::get_ratecard_preloaded()->data->discount_types;

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
        $discount_class_id = $request->discount_class_id;
        $discount_type_value = $request->discount_type_value;
        $percent_value = (int) $request->percent_value;
        $percent_start_date = strtotime($request->percent_start_date);
        $percent_stop_date = strtotime($request->percent_stop_date);
        $value = (int) $request->value;
        $value_start_date = strtotime($request->value_start_date);
        $value_stop_date = strtotime($request->value_stop_date);

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

        $discount = json_decode(Api::create_discount(
            $discount_type_value, $percent_value, $percent_start_date,
            $percent_stop_date, $value, $value_start_date, $value_stop_date,
            $discount_class_id, $discount_type_id, $discount_type_sub_value
        ));

        if($discount->status === false) {
            return redirect()->back()->with('error', $discount->message);
        } else {
            return redirect()->back()->with('success', trans('app.discount_created'));
        }
    }

    public function update(Request $request, $discount)
    {
        $number_value = (int) $request->value;
        $percent_value = (int) $request->percent_value;

        $classes = Api::get_ratecard_preloaded()->data->discount_classes;
        $types = Api::get_ratecard_preloaded()->data->discount_types;

        $hourly_ranges = Api::get_ratecard_preloaded()->data->hourly_range;
        $day_parts = Api::get_ratecard_preloaded()->data->day_parts;
        $agencies = Api::get_ratecard_preloaded()->data->agency;

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
        $discount_class_id = $request->discount_class_id;
        $discount_type_value = $request->discount_type_value;
        $percent_value = (int) $request->percent_value;
        $percent_start_date = strtotime($request->percent_start_date);
        $percent_stop_date = strtotime($request->percent_stop_date);
        $value = (int) $request->value;
        $value_start_date = strtotime($request->value_start_date);
        $value_stop_date = strtotime($request->value_stop_date);

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

        $discount = json_decode(Api::update_discount(
            $discount, $discount_type_value, $percent_value, $percent_start_date,
            $percent_stop_date, $value, $value_start_date, $value_stop_date,
            $discount_class_id, $discount_type_id, $discount_type_sub_value
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
