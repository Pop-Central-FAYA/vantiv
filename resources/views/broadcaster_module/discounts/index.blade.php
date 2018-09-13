@extends('layouts.faya_app')

@section('title')
    <title> FAYA | Discounts </title>
@stop

@section('content')
    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')

    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Discounts</h2>
            </div>
        </div>



        <!-- campaign details -->
        <div class="the_frame client_dets mb4">

            <!-- tab links -->
            <div class="tab_header m4 border_bottom clearfix">
                <a href="#agency">Agency</a>
                <a href="#brands">Brands</a>
                <a href="#time">Time</a>
                <a href="#day">Day Parts</a>
                <a href="#price">Price</a>
            </div>

            <div class="tab_contain">

                <!-- sales reports -->
                <div class="tab_content" id="agency">

                    <!-- filter -->
                    <div class="filters clearfix">
                        <div class="column col_7">
                            <div class="header_search col_6">
                                <form>
                                    <input type="text" placeholder="Search...">
                                </form>
                            </div>
                        </div>

                        <div class="column col_5 clearfix">

                            <div class="col_6 right align_right">
                                <a href="#new_discount_agency" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>

                            <div class="right col_4">
                                <div class="select_wrap">
                                    <select>
                                        <option>Filter</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- end -->
                    <div class="similar_table pt3">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Agency</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($agency_discounts as $agency_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ $agency_discount->name }}</div>
                                <div class="column col_1">{{ $agency_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($agency_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($agency_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $agency_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($agency_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($agency_discount->value_stop_date)) }}</div>
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>

                                        <div class="more_more">
                                            <a href="#edit_client" class="modal_click">Edit</a>
                                            <a href="" class="color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        @endforeach
                    <!-- table item end -->
                    </div>

                </div>
                <!-- end -->


                <!-- Brands -->
                <div class="tab_content" id="brands">

                    <!-- filter -->
                    <div class="filters clearfix">
                        <div class="column col_7">
                            <div class="header_search col_6">
                                <form>
                                    <input type="text" placeholder="Search...">
                                </form>
                            </div>
                        </div>

                        <div class="column col_5 clearfix">

                            <div class="col_6 right align_right">
                                <a href="#new_discount_brand" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>

                            <div class="right col_4">
                                <div class="select_wrap">
                                    <select>
                                        <option>Filter</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- end -->

                    <div class="similar_table pt3">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Brand</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($brand_discounts as $brand_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ ucfirst($brand_discount->name) }}</div>
                                <div class="column col_1">{{ $brand_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($brand_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($brand_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $brand_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($brand_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($brand_discount->value_stop_date)) }}</div>
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>

                                        <div class="more_more">
                                            <a href="#edit_client" class="modal_click">Edit</a>
                                            <a href="" class="color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>

                            </div>
                    @endforeach
                    <!-- table item end -->
                    </div>

                </div>
                <!-- end -->


                <!-- Time -->
                <div class="tab_content" id="time">

                    <!-- filter -->
                    <div class="filters clearfix">
                        <div class="column col_7">
                            <div class="header_search col_6">
                                <form>
                                    <input type="text" placeholder="Search...">
                                </form>
                            </div>
                        </div>

                        <div class="column col_5 clearfix">

                            <div class="col_6 right align_right">
                                <a href="#new_discount_time" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>

                            <div class="right col_4">
                                <div class="select_wrap">
                                    <select>
                                        <option>Filter</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- end -->

                    <div class="similar_table pt3">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Agency</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($time_discounts as $time_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ $time_discount->hourly_range }}</div>
                                <div class="column col_1">{{ $time_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($time_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($time_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $time_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($time_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($time_discount->value_stop_date)) }}</div>
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>

                                        <div class="more_more">
                                            <a href="#edit_client" class="modal_click">Edit</a>
                                            <a href="" class="color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>

                            </div>
                    @endforeach
                    <!-- table item end -->
                    </div>

                </div>
                <!-- end -->


                <!-- Day -->
                <div class="tab_content clearfix" id="day">

                    <!-- filter -->
                    <div class="filters clearfix">
                        <div class="column col_7">
                            <div class="header_search col_6">
                                <form>
                                    <input type="text" placeholder="Search...">
                                </form>
                            </div>
                        </div>

                        <div class="column col_5 clearfix">

                            <div class="col_6 right align_right">
                                <a href="#new_discount_day" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>

                            <div class="right col_4">
                                <div class="select_wrap">
                                    <select>
                                        <option>Filter</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- end -->

                    <div class="similar_table pt3">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Day Parts</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($daypart_discounts as $daypart_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ $daypart_discount->day_part }}</div>
                                <div class="column col_1">{{ $daypart_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($daypart_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($daypart_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $daypart_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($daypart_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($daypart_discount->value_stop_date)) }}</div>
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>

                                        <div class="more_more">
                                            <a href="#edit_client" class="modal_click">Edit</a>
                                            <a href="" class="color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>

                            </div>
                    @endforeach
                    <!-- table item end -->
                    </div>

                </div>
                <!-- end -->


                <!-- Price -->
                <div class="tab_content" id="price">

                    <!-- filter -->
                    <div class="filters clearfix">
                        <div class="column col_7">
                            <div class="header_search col_6">
                                <form>
                                    <input type="text" placeholder="Search...">
                                </form>
                            </div>
                        </div>

                        <div class="column col_5 clearfix">

                            <div class="col_6 right align_right">
                                <a href="#new_discount_price" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>

                            <div class="right col_4">
                                <div class="select_wrap">
                                    <select>
                                        <option>Filter</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- end -->

                    <div class="similar_table pt3">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Price Range</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($price_discounts as $price_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ number_format($price_discount->discount_type_value,2) . ' - ' . number_format($price_discount->discount_type_sub_value,2)  }}</div>
                                <div class="column col_1">{{ $price_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($price_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($price_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $price_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($price_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($price_discount->value_stop_date)) }}</div>
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>

                                        <div class="more_more">
                                            <a href="#edit_client" class="modal_click">Edit</a>
                                            <a href="" class="color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>

                            </div>
                    @endforeach
                    <!-- table item end -->
                    </div>

                </div>
                <!-- end -->



            </div>

        </div>


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
        <!-- end discount modal -->


    </div><!-- main contain -->

@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script>
        $(document).ready(function () {
            flatpickr(".flatpickr", {
                altInput: true,
            });
        })
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <style>
        ._table_item > div:first-child {
            padding-top: 12px;
            font-size: 16px;
            @import;
        }
    </style>
@stop