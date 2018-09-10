@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create - Adslots</title>
@stop

@section('content')

    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')

    <div class="main_contain">
        {{--Header--}}
        @include('partials.new-frontend.broadcaster.header')
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Ad Slots</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color pt">

            <div class="margin_center col_11 clearfix pt4 create_fields">


                <div class="clearfix mb3">
                    <div class="input_wrap column col_4">
                        <label class="small_faint">Day</label>
                        <div class="select_wrap">
                            <select name="days">
                                <option>Select Day</option>
                                @foreach($days as $day)
                                    <option value="{{ $day->id }}">{{ $day->day }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="input_wrap column col_4">
                        <label class="small_faint">Hourly Range</label>
                        <div class="select_wrap">
                            <select name="hourly_ranges">
                                <option>Select Hourly Range</option>
                                @foreach($hours as $hour)
                                    <option value="{{ $hour->id }}">{{ $hour->time_range }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                <p class='m-b'>Hourly Range Breakdown</p>

                <!-- start -->
                <div class="clearfix m-b">
                    <div class="input_wrap column col_2">
                        <label class="small_faint">Start</label>

                        <div class="select_wrap">
                            <select>
                                <option>00:00</option>
                                <option></option>
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="input_wrap column col_2">
                        <label class="small_faint">End</label>

                        <div class="select_wrap">
                            <select>
                                <option>00:00</option>
                                <option></option>
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="input_wrap column col_2">
                        <label class="small_faint">60 Seconds</label>
                        <input type="text" placeholder="Enter Price">
                    </div>

                    <div class="input_wrap column col_2">
                        <label class="small_faint">45 Seconds</label>
                        <input type="text" placeholder="Enter Price">
                    </div>

                    <div class="input_wrap column col_2">
                        <label class="small_faint">30 Seconds</label>
                        <input type="text" placeholder="Enter Price">
                    </div>

                    <div class="input_wrap column col_2">
                        <label class="small_faint">15 Seconds</label>
                        <input type="text" placeholder="Enter Price">
                    </div>
                </div>
                <!-- end -->

                <!-- start -->
                <div class="clearfix m-b">
                    <div class="input_wrap column col_3">
                        <label class="small_faint">Day Parts</label>

                        <div class="select_wrap">
                            <select>
                                <option>Select Day Parts</option>
                                <option></option>
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="input_wrap column col_2">
                        <label class="small_faint">Region</label>

                        <div class="select_wrap">
                            <select>
                                <option>Select</option>
                                <option></option>
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="input_wrap column col_3">
                        <label class="small_faint">Target Audience</label>
                        <div class="select_wrap">
                            <select>
                                <option>Select</option>
                                <option></option>
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="input_wrap column col_2">
                        <label class="small_faint">Minimum Age</label>
                        <input type="text" placeholder="Enter Age">
                    </div>

                    <div class="input_wrap column col_2">
                        <label class="small_faint">Maximum Age</label>
                        <input type="text" placeholder="Enter Age">
                    </div>
                </div>
                <!-- end -->

                <div class="align_right mb3">
                    <a href="" class="uppercased color_initial">Add More</a>
                </div>


                <div class="mb4 align_right pt">
                    <a href="" class="padd color_initial light_font" onclick="">Cancel</a>
                    <input type="submit" value="Create Ad Spot" class="btn uppercased mb4">
                </div>

            </div>
        </div>
        <!-- main frame end -->

    </div><!-- main contain -->
@stop