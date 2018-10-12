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
                                            <a href="#edit_discount_agency{{ $agency_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_agency{{ $agency_discount->id }}" class="modal_click color_red">Delete</a>
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
                                            <a href="#edit_discount_brand{{ $brand_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_brand{{ $brand_discount->id }}" class="modal_click color_red">Delete</a>
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
                                            <a href="#edit_discount_time{{ $time_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_time{{ $time_discount->id }}" class="modal_click color_red">Delete</a>
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
                                            <a href="#edit_discount_dayparts{{ $daypart_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_daypart{{ $daypart_discount->id }}" class="modal_click color_red">Delete</a>
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
                                            <a href="#edit_discount_price{{ $price_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_price{{ $price_discount->id }}" class="modal_click color_red">Delete</a>
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



        <!-- end discount modal -->
        {{--add modal--}}
        @include('broadcaster_module.discounts.add_modal')

        {{--edit modals--}}
        @include('broadcaster_module.discounts.edit_modal')

        {{--delete modal--}}
        @include('broadcaster_module.discounts.delete_modal')



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