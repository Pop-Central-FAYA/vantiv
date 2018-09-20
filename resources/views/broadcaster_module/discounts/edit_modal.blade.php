{{--edit modal for agency--}}
@foreach($agency_discounts as $agency_discount)
    <div class="modal_contain" id="edit_discount_agency{{ $agency_discount->id }}">
        <h2 class="sub_header mb4">New Discount</h2>
        <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $agency_discount->id]) }}">
            {{ csrf_field() }}
            <input type="hidden" name="discount_type_id" value="{{ $types['Agency'] }}">
            <input type="hidden" name="discount_type_value" value="{{ $agency_discount->discount_type_value }}">
            <input type="hidden" name="discount_type_sub_value" value="{{ $agency_discount->discount_type_sub_value }}">
            <div class="input_wrap">
                <label class="small_faint">Agency</label>
                <div class="select_wrap">
                    <select name="discount_type_value" required disabled>
                        <option value="">{{ $agency_discount->name }}</option>
                    </select>
                </div>
            </div>

            <div class="clearfix">

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="number" name="value" value="{{ $agency_discount->value }}" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" name="value_start_date" class="flatpickr" value="{{ $agency_discount->value_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" name="value_stop_date" class="flatpickr" value="{{ $agency_discount->value_stop_date }}" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="number" name="percent_value" value="{{ $agency_discount->percent_value }}" placeholder="Enter % value">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" name="percent_start_date" class="flatpickr" value="{{ $agency_discount->percent_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" name="percent_stop_date" class="flatpickr" value="{{ $agency_discount->percent_stop_date }}" placeholder="Select date">
                </div>
            </div>

            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Update Discount" class="btn uppercased">
            </div>

        </form>
        </div>
@endforeach

{{--brand update--}}
@foreach($brand_discounts as $brand_discount)
    <div class="modal_contain" id="edit_discount_brand{{ $brand_discount->id }}">
        <h2 class="sub_header mb4">New Discount</h2>
        <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $brand_discount->id]) }}">
            {{ csrf_field() }}
            <input type="hidden" name="discount_type_id" value="{{ $types['Brands'] }}">
            <input type="hidden" name="discount_type_value" value="{{ $brand_discount->discount_type_value }}">
            <input type="hidden" name="discount_type_sub_value" value="{{ $brand_discount->discount_type_sub_value }}">
            <div class="input_wrap">
                <label class="small_faint">Brand</label>
                <div class="select_wrap">
                    <select name="discount_type_value" required disabled>
                        <option value="">{{ $brand_discount->name }}</option>
                    </select>
                </div>
            </div>

            <div class="clearfix">

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="number" name="value" value="{{ $brand_discount->value }}" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" name="value_start_date" class="flatpickr" value="{{ $brand_discount->value_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" name="value_stop_date" class="flatpickr" value="{{ $brand_discount->value_stop_date }}" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="number" name="percent_value" value="{{ $brand_discount->percent_value }}" placeholder="Enter % value">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" name="percent_start_date" class="flatpickr" value="{{ $brand_discount->percent_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" name="percent_stop_date" class="flatpickr" value="{{ $brand_discount->percent_stop_date }}" placeholder="Select date">
                </div>
            </div>

            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Update Discount" class="btn uppercased">
            </div>

        </form>
    </div>
@endforeach

{{--time update--}}
@foreach($time_discounts as $time_discount)
    <div class="modal_contain" id="edit_discount_time{{ $time_discount->id }}">
        <h2 class="sub_header mb4">New Discount</h2>
        <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $time_discount->id]) }}">
            {{ csrf_field() }}
            <input type="hidden" name="discount_type_id" value="{{ $types['Time'] }}">
            <input type="hidden" name="discount_type_value" value="{{ $time_discount->discount_type_value }}">
            <input type="hidden" name="discount_type_sub_value" value="{{ $time_discount->discount_type_sub_value }}">
            <div class="input_wrap">
                <label class="small_faint">Time</label>
                <div class="select_wrap">
                    <select name="discount_type_value" required disabled>
                        <option value="">{{ $time_discount->hourly_range }}</option>
                    </select>
                </div>
            </div>

            <div class="clearfix">

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="number" name="value" value="{{ $time_discount->value }}" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" name="value_start_date" class="flatpickr" value="{{ $time_discount->value_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" name="value_stop_date" class="flatpickr" value="{{ $time_discount->value_stop_date }}" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="number" name="percent_value" value="{{ $time_discount->percent_value }}" placeholder="Enter % value">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" name="percent_start_date" class="flatpickr" value="{{ $time_discount->percent_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" name="percent_stop_date" class="flatpickr" value="{{ $time_discount->percent_stop_date }}" placeholder="Select date">
                </div>
            </div>

            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Update Discount" class="btn uppercased">
            </div>

        </form>
    </div>
@endforeach

{{--dayparts update--}}
@foreach($daypart_discounts as $daypart_discount)
    <div class="modal_contain" id="edit_discount_dayparts{{ $daypart_discount->id }}">
        <h2 class="sub_header mb4">New Discount</h2>
        <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $daypart_discount->id]) }}">
            {{ csrf_field() }}
            <input type="hidden" name="discount_type_id" value="{{ $types['Day Part'] }}">
            <input type="hidden" name="discount_type_value" value="{{ $daypart_discount->discount_type_value }}">
            <input type="hidden" name="discount_type_sub_value" value="{{ $daypart_discount->discount_type_sub_value }}">
            <div class="input_wrap">
                <label class="small_faint">Time</label>
                <div class="select_wrap">
                    <select name="discount_type_value" required disabled>
                        <option value="">{{ $daypart_discount->day_part }}</option>
                    </select>
                </div>
            </div>

            <div class="clearfix">

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="number" name="value" value="{{ $daypart_discount->value }}" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" name="value_start_date" class="flatpickr" value="{{ $daypart_discount->value_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" name="value_stop_date" class="flatpickr" value="{{ $daypart_discount->value_stop_date }}" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="number" name="percent_value" value="{{ $daypart_discount->percent_value }}" placeholder="Enter % value">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" name="percent_start_date" class="flatpickr" value="{{ $daypart_discount->percent_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" name="percent_stop_date" class="flatpickr" value="{{ $daypart_discount->percent_stop_date }}" placeholder="Select date">
                </div>
            </div>

            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Update Discount" class="btn uppercased">
            </div>

        </form>
    </div>
@endforeach

{{--price update--}}
@foreach($price_discounts as $price_discount)
    <div class="modal_contain" id="edit_discount_price{{ $price_discount->id }}">
        <h2 class="sub_header mb4">New Discount</h2>
        <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $price_discount->id]) }}">
            {{ csrf_field() }}
            <input type="hidden" name="discount_type_id" value="{{ $types['Price'] }}">
            {{--<input type="hidden" name="discount_type_value" value="{{ $price_discount->discount_type_value }}">--}}
            {{--<input type="hidden" name="discount_type_sub_value" value="{{ $price_discount->discount_type_sub_value }}">--}}
            <div class="clearfix">
                <div class="input_wrap column col_6">
                    <label class="small_faint weight_medium">Min. Value</label>
                    <input type="number" required name="discount_type_value" value="{{ $price_discount->discount_type_value }}" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_6">
                    <label class="small_faint weight_medium">Max. Value</label>
                    <input type="number" required name="discount_type_sub_value" value="{{ $price_discount->discount_type_sub_value }}" placeholder="Enter amount">
                </div>
            </div>

            <div class="clearfix">

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="number" name="value" value="{{ $price_discount->value }}" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" name="value_start_date" class="flatpickr" value="{{ $price_discount->value_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" name="value_stop_date" class="flatpickr" value="{{ $price_discount->value_stop_date }}" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="number" name="percent_value" value="{{ $price_discount->percent_value }}" placeholder="Enter % value">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" name="percent_start_date" class="flatpickr" value="{{ $price_discount->percent_start_date }}" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" name="percent_stop_date" class="flatpickr" value="{{ $price_discount->percent_stop_date }}" placeholder="Select date">
                </div>
            </div>

            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Update Discount" class="btn uppercased">
            </div>

        </form>
    </div>
@endforeach