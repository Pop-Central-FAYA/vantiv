<!-- new agency discount modal -->
<div class="modal_contain" id="new_discount_agency">
    <h2 class="sub_header mb4">New Discount</h2>
    <form action="{{ route('discount.store') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="discount_type_id" value="{{ $types[0]->id }}">
        <div class="input_wrap">
            <label class="small_faint">Agency</label>
            <div class="select_wrap">
                <select name="discount_type_value" required>
                    <option value="">Select Agency</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->agency_id }}">{{ $agency->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="clearfix">

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Amount Value</label>
                <input type="number" name="value" placeholder="Enter amount">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value Start</label>
                <input type="text" name="value_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value End</label>
                <input type="text" name="value_stop_date" class="flatpickr" placeholder="Select Date">
            </div>
        </div>

        <div class="clearfix">
            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Value</label>
                <input type="number" name="percent_value" placeholder="Enter % value">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Start</label>
                <input type="text" name="percent_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% End</label>
                <input type="text" name="percent_stop_date" class="flatpickr" placeholder="Select date">
            </div>
        </div>

        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <input type="submit" value="Add Discount" class="btn uppercased">
        </div>

    </form>
</div>
<!-- end discount modal -->

<!-- new brand discount modal -->
<div class="modal_contain" id="new_discount_brand">
    <h2 class="sub_header mb4">New Discount</h2>

    <form action="{{ route('discount.store') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="discount_type_id" value="{{ $types[1]->id }}">

        <div class="input_wrap">
            <label class="small_faint">Brand</label>
            <div class="select_wrap">
                <select name="discount_type_value" required>
                    <option>Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="clearfix">

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Amount Value</label>
                <input type="number" name="value" placeholder="Enter amount">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value Start</label>
                <input type="text" name="value_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value End</label>
                <input type="text" name="value_stop_date" class="flatpickr" placeholder="Select Date">
            </div>
        </div>

        <div class="clearfix">
            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Value</label>
                <input type="number" name="percent_value" placeholder="Enter % value">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Start</label>
                <input type="text" name="percent_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% End</label>
                <input type="text" name="percent_stop_date" class="flatpickr" placeholder="Select date">
            </div>
        </div>


        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <input type="submit" value="Add Discount" class="btn uppercased">
        </div>
    </form>

</div>
<!-- end discount modal -->

<!-- new time discount modal -->
<div class="modal_contain" id="new_discount_time">
    <h2 class="sub_header mb4">New Discount</h2>

    <form action="{{ route('discount.store') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="discount_type_id" value="{{ $types[2]->id }}">

        <div class="input_wrap">
            <label class="small_faint">Time</label>
            <div class="select_wrap">
                <select name="discount_type_value" required>
                    <option>Select Time</option>
                    @foreach($hourly_ranges as $hourly_range)
                        <option value="{{ $hourly_range->id }}">{{ $hourly_range->time_range }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="clearfix">

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Amount Value</label>
                <input type="number" name="value" placeholder="Enter amount">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value Start</label>
                <input type="text" name="value_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value End</label>
                <input type="text" name="value_stop_date" class="flatpickr" placeholder="Select Date">
            </div>
        </div>

        <div class="clearfix">
            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Value</label>
                <input type="number" name="percent_value" placeholder="Enter % value">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Start</label>
                <input type="text" name="percent_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% End</label>
                <input type="text" name="percent_stop_date" class="flatpickr" placeholder="Select date">
            </div>
        </div>


        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <input type="submit" value="Add Discount" class="btn uppercased">
        </div>
    </form>
</div>
<!-- end discount modal -->

<!-- new day discount modal -->
<div class="modal_contain" id="new_discount_day">
    <h2 class="sub_header mb4">New Discount</h2>

    <form action="{{ route('discount.store') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="discount_type_id" value="{{ $types[3]->id }}">

        <div class="input_wrap">
            <label class="small_faint">Day Part</label>
            <div class="select_wrap">
                <select name="discount_type_value" required>
                    <option>Select Day Parts</option>
                    @foreach($day_parts as $day_part)
                        <option value="{{ $day_part->id }}">{{ $day_part->day_parts }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="clearfix">

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Amount Value</label>
                <input type="number" name="value" placeholder="Enter amount">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value Start</label>
                <input type="text" name="value_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value End</label>
                <input type="text" name="value_stop_date" class="flatpickr" placeholder="Select Date">
            </div>
        </div>

        <div class="clearfix">
            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Value</label>
                <input type="number" name="percent_value" placeholder="Enter % value">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Start</label>
                <input type="text" name="percent_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% End</label>
                <input type="text" name="percent_stop_date" class="flatpickr" placeholder="Select date">
            </div>
        </div>


        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <input type="submit" value="Add Discount" class="btn uppercased">
        </div>
    </form>
</div>
<!-- end discount modal -->

<!-- new price discount modal -->
<div class="modal_contain" id="new_discount_price">
    <h2 class="sub_header mb4">New Discount</h2>

    <form action="{{ route('discount.store') }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="discount_type_id" value="{{ $types[4]->id }}">

        <div class="clearfix">
            <div class="input_wrap column col_6">
                <label class="small_faint weight_medium">Min. Value</label>
                <input type="number" required name="discount_type_value" placeholder="Enter amount">
            </div>

            <div class="input_wrap column col_6">
                <label class="small_faint weight_medium">Max. Value</label>
                <input type="number" required name="discount_type_sub_value" placeholder="Enter amount">
            </div>
        </div>

        <div class="clearfix">

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Amount Value</label>
                <input type="number" name="value" placeholder="Enter amount">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value Start</label>
                <input type="text" name="value_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">Value End</label>
                <input type="text" name="value_stop_date" class="flatpickr" placeholder="Select Date">
            </div>
        </div>

        <div class="clearfix">
            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Value</label>
                <input type="number" name="percent_value" placeholder="Enter % value">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% Start</label>
                <input type="text" name="percent_start_date" class="flatpickr" placeholder="Select date">
            </div>

            <div class="input_wrap column col_4">
                <label class="small_faint weight_medium">% End</label>
                <input type="text" name="percent_stop_date" class="flatpickr" placeholder="Select date">
            </div>
        </div>


        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <input type="submit" value="Add Discount" class="btn uppercased">
        </div>
    </form>
</div>