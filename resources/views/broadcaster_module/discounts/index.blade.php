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
                <a href="#surcharge">Surcharge</a>
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

                    <table>
                        <tr>
                            <th>Agency</th>
                            <th>% Discount</th>
                            <th>% Start Date</th>
                            <th>% Stop Date</th>
                            <th>Discount Amount</th>
                            <th>Amount Start Date</th>
                            <th>Amount Stop Date</th>
                            <th></th>
                        </tr>

                        <tr>
                            <td>Coca Cola</td>
                            <td>10</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>&#8358; 23,000</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>
                                <div class="table_nav">
                                    <span class="icon ion-more"></span>

                                    <div>
                                        <a href="">Edit</a>
                                        <a href="" class="color_red">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>


                    </table>

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

                    <table>
                        <tr>
                            <th>Agency</th>
                            <th>% Discount</th>
                            <th>% Start Date</th>
                            <th>% Stop Date</th>
                            <th>Discount Amount</th>
                            <th>Amount Start Date</th>
                            <th>Amount Stop Date</th>
                            <th></th>
                        </tr>

                        <tr>
                            <td>Coca Cola</td>
                            <td>10</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>&#8358; 23,000</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>
                                <div class="table_nav">
                                    <span class="icon ion-more"></span>

                                    <div>
                                        <a href="">Edit</a>
                                        <a href="" class="color_red">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>


                    </table>

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

                    <table>
                        <tr>
                            <th>Agency</th>
                            <th>% Discount</th>
                            <th>% Start Date</th>
                            <th>% Stop Date</th>
                            <th>Discount Amount</th>
                            <th>Amount Start Date</th>
                            <th>Amount Stop Date</th>
                            <th></th>
                        </tr>

                        <tr>
                            <td>Coca Cola</td>
                            <td>10</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>&#8358; 23,000</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>
                                <div class="table_nav">
                                    <span class="icon ion-more"></span>

                                    <div>
                                        <a href="">Edit</a>
                                        <a href="" class="color_red">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>


                    </table>

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

                    <table>
                        <tr>
                            <th>Agency</th>
                            <th>% Discount</th>
                            <th>% Start Date</th>
                            <th>% Stop Date</th>
                            <th>Discount Amount</th>
                            <th>Amount Start Date</th>
                            <th>Amount Stop Date</th>
                            <th></th>
                        </tr>

                        <tr>
                            <td>Coca Cola</td>
                            <td>10</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>&#8358; 23,000</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>
                                <div class="table_nav">
                                    <span class="icon ion-more"></span>

                                    <div>
                                        <a href="">Edit</a>
                                        <a href="" class="color_red">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>


                    </table>

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

                    <table>
                        <tr>
                            <th>Agency</th>
                            <th>% Discount</th>
                            <th>% Start Date</th>
                            <th>% Stop Date</th>
                            <th>Discount Amount</th>
                            <th>Amount Start Date</th>
                            <th>Amount Stop Date</th>
                            <th></th>
                        </tr>

                        <tr>
                            <td>Coca Cola</td>
                            <td>10</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>&#8358; 23,000</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>
                                <div class="table_nav">
                                    <span class="icon ion-more"></span>

                                    <div>
                                        <a href="">Edit</a>
                                        <a href="" class="color_red">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>


                    </table>

                </div>
                <!-- end -->

                <!-- Surcharge -->
                <div class="tab_content" id="surcharge">

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
                                <a href="#new_discount_surcharge" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
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

                    <table>
                        <tr>
                            <th>Agency</th>
                            <th>% Discount</th>
                            <th>% Start Date</th>
                            <th>% Stop Date</th>
                            <th>Discount Amount</th>
                            <th>Amount Start Date</th>
                            <th>Amount Stop Date</th>
                            <th></th>
                        </tr>

                        <tr>
                            <td>Coca Cola</td>
                            <td>10</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>&#8358; 23,000</td>
                            <td>21/May/18</td>
                            <td>21/May/18</td>
                            <td>
                                <div class="table_nav">
                                    <span class="icon ion-more"></span>

                                    <div>
                                        <a href="">Edit</a>
                                        <a href="" class="color_red">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>


                    </table>

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
                                <option value="{{ $agency->user_id }}">{{ $agency->name }}</option>
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

            <div class="input_wrap">
                <label class="small_faint">Brand</label>
                <div class="select_wrap">
                    <select>
                        <option>Select Brand</option>
                        <option></option>
                        <option></option>
                    </select>
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" placeholder="Select date">
                </div>
            </div>


            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Add Discount" class="btn uppercased">
            </div>
        </div>
        <!-- end discount modal -->

        <!-- new time discount modal -->
        <div class="modal_contain" id="new_discount_time">
            <h2 class="sub_header mb4">New Discount</h2>

            <div class="input_wrap">
                <label class="small_faint">Time</label>
                <div class="select_wrap">
                    <select>
                        <option>Select Time</option>
                        <option></option>
                        <option></option>
                    </select>
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" placeholder="Select date">
                </div>
            </div>


            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Add Discount" class="btn uppercased">
            </div>
        </div>
        <!-- end discount modal -->

        <!-- new day discount modal -->
        <div class="modal_contain" id="new_discount_day">
            <h2 class="sub_header mb4">New Discount</h2>

            <div class="input_wrap">
                <label class="small_faint">Day</label>
                <div class="select_wrap">
                    <select>
                        <option>Select Day</option>
                        <option></option>
                        <option></option>
                    </select>
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" placeholder="Select date">
                </div>
            </div>


            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Add Discount" class="btn uppercased">
            </div>
        </div>
        <!-- end discount modal -->

        <!-- new price discount modal -->
        <div class="modal_contain" id="new_discount_price">
            <h2 class="sub_header mb4">New Discount</h2>

            <div class="input_wrap">
                <label class="small_faint weight_medium">Agency</label>
                <input type="text" placeholder="Enter Agency Name">
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" placeholder="Select date">
                </div>
            </div>


            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Add Discount" class="btn uppercased">
            </div>
        </div>
        <!-- end discount modal -->

        <!-- new surcharge discount modal -->
        <div class="modal_contain" id="new_discount_surcharge">
            <h2 class="sub_header mb4">New Discount</h2>

            <div class="input_wrap">
                <label class="small_faint weight_medium">Agency</label>
                <input type="text" placeholder="Enter Agency Name">
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Amount Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">Value End</label>
                    <input type="text" placeholder="Select Date">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Value</label>
                    <input type="text" placeholder="Enter amount">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% Start</label>
                    <input type="text" placeholder="Select date">
                </div>

                <div class="input_wrap column col_4">
                    <label class="small_faint weight_medium">% End</label>
                    <input type="text" placeholder="Select date">
                </div>
            </div>


            <div class="align_right pt3">
                <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
                <input type="submit" value="Add Discount" class="btn uppercased">
            </div>
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
@stop