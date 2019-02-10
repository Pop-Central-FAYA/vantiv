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
            <form action="{{ route('adslot.store') }}" method="post">
                {{ csrf_field() }}
                <div class="margin_center col_11 clearfix pt4 create_fields">

                <div class="clearfix mb3">
                    <div class="input_wrap column col_4">
                        <label class="small_faint">Day</label>
                        <div class="select_wrap">
                            <select required name="days">
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
                            <select required name="hourly_ranges">
                                <option>Select Hourly Range</option>
                                @foreach($hours as $hour)
                                    <option value="{{ $hour->id }}">{{ $hour->time_range }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                <p class='m-b'>Hourly Range Breakdown</p>

                <div class="clearfix m-b" id="dynamic_field">
                    <!-- start -->
                    <div class="clearfix m-b b">
                        <div class="input_wrap column col_2">
                            <label class="small_faint">Start</label>
                            <input type="text" id="timepicker" required name="from_time[]" class="timepicker"/>
                        </div>

                        <div class="input_wrap column col_2">
                            <label class="small_faint">End</label>
                            <input type="text" id="timepicker" required name="to_time[]" class="timepicker"/>
                        </div>

                        <div class="input_wrap column col_2">
                            <label class="small_faint">60 Seconds</label>
                            <input type="text" name="price_60[]" required placeholder="Enter Price">
                        </div>

                        <div class="input_wrap column col_2">
                            <label class="small_faint">45 Seconds</label>
                            <input type="text" name="price_45[]" required placeholder="Enter Price">
                        </div>

                        <div class="input_wrap column col_2">
                            <label class="small_faint">30 Seconds</label>
                            <input type="text" name="price_30[]" required placeholder="Enter Price">
                        </div>

                        <div class="input_wrap column col_2">
                            <label class="small_faint">15 Seconds</label>
                            <input type="text" name="price_15[]" required placeholder="Enter Price">
                        </div>
                    </div>
                    <!-- end -->

                    <!-- start -->
                    <div class="clearfix m-b">
                        <div class="input_wrap column col_3">
                            <label class="small_faint">Day Parts</label>

                            <div class="select_wrap">
                                <select name="dayparts[]" class="b" required>
                                    <option>Select Day Parts</option>
                                    @foreach ($day_parts as $daypart)
                                        <option value="{{ $daypart->id }}">{{ $daypart->day_parts }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="input_wrap column col_2">
                            <label class="small_faint">Region</label>

                            <div class="select_wrap">
                                <select name="regions[]" required>
                                    <option>Select</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->region }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="input_wrap column col_3">
                            <label class="small_faint">Target Audience</label>
                            <div class="select_wrap">
                                <select name="target_audiences[]" required>
                                    <option>Select</option>
                                    @foreach($target_audiences as $target_audience)
                                        <option value="{{ $target_audience->id }}">{{ $target_audience->audience }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="input_wrap column col_2">
                            <label class="small_faint">Minimum Age</label>
                            <input type="text" name="min_age[]" required placeholder="Enter Age">
                        </div>

                        <div class="input_wrap column col_2">
                            <label class="small_faint">Maximum Age</label>
                            <input type="text" name="max_age[]" required placeholder="Enter Age">
                        </div>
                    </div>

                </div>
                    <!-- end -->

                    <div class="align_right mb3">
                        <a href="" id="add_more" class="uppercased color_initial">Add More</a>
                    </div>

                <div class="mb4 align_right pt">
                    <input type="submit" value="Create Ad Spot" class="btn uppercased mb4">
                </div>

            </div>
            </form>
        </div>
        <!-- main frame end -->

    </div><!-- main contain -->
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('new_frontend/js/wickedpicker.min.js') }}"></script>
    <script>
        <?php
        if (count($target_audiences) > 0) {
            echo "var target_audiences = " . json_encode($target_audiences) . ";\n";
        }

        if (count($regions) > 0) {
            echo "var regions = " . json_encode($regions) . ";\n";
        }

        if (count($day_parts) > 0) {
            echo "var day_parts = " . json_encode($day_parts) . ";\n";
        }
        ?>
        $(document).ready(function () {
            var i = $("select .b").length;
            var max = 12;
            $("#add_more").click(function() {
                event.preventDefault();
                i++;
                if (i >= max) {
                    return false;
                }

                var big_html = '';
                big_html += '<div class="remove_div'+i+'"><hr><div class="clearfix m-b">\n' +
        '                        <div class="input_wrap column col_2">\n' +
        '                            <label class="small_faint">Start</label>\n' +
        '                            <input type="text" id="timepicker" required name="from_time[]" class="timepicker"/>\n' +
        '                        </div>\n' +
        '\n' +
        '                        <div class="input_wrap column col_2">\n' +
        '                            <label class="small_faint">End</label>\n' +
        '                            <input type="text" id="timepicker" required name="to_time[]" class="timepicker"/>\n' +
        '                        </div>\n' +
        '\n' +
        '                        <div class="input_wrap column col_2">\n' +
        '                            <label class="small_faint">60 Seconds</label>\n' +
        '                            <input type="text" name="price_60[]" required placeholder="Enter Price">\n' +
        '                        </div>\n' +
        '\n' +
        '                        <div class="input_wrap column col_2">\n' +
        '                            <label class="small_faint">45 Seconds</label>\n' +
        '                            <input type="text" name="price_45[]" required placeholder="Enter Price">\n' +
        '                        </div>\n' +
        '\n' +
        '                        <div class="input_wrap column col_2">\n' +
        '                            <label class="small_faint">30 Seconds</label>\n' +
        '                            <input type="text" name="price_30[]" required placeholder="Enter Price">\n' +
        '                        </div>\n' +
        '\n' +
        '                        <div class="input_wrap column col_2">\n' +
        '                            <label class="small_faint">15 Seconds</label>\n' +
        '                            <input type="text" name="price_15[]" required placeholder="Enter Price">\n' +
        '                        </div>\n' +
        '                    </div>';

                big_html += '<div class="clearfix m-b">\n' +
    '                        <div class="input_wrap column col_3">\n' +
'                            <label class="small_faint">Day Parts</label>\n' +
'\n' +
'                            <div class="select_wrap">\n' +
'                                <select name="dayparts[]" required>\n' +
'                                    <option>Select Day Parts</option>';
                                    $.each(day_parts, function (index,value) {
                                        big_html += '<option value="'+ value.id +'">'+ value.day_parts +'</option>'
                                    });
                                    big_html += '</select>\n' +
                                        '                            </div>\n' +
                                        '                        </div>\n' +
                                        '\n' +
                                        '                        <div class="input_wrap column col_2">\n' +
                                        '                            <label class="small_faint">Region</label>\n' +
                                        '\n' +
                                        '                            <div class="select_wrap">\n' +
                                        '                                <select name="regions[]" required>\n' +
                                        '                                    <option>Select</option>';
                                    $.each(regions, function (index,value) {
                                        big_html += '<option value ="'+ value.id + '"> ' + value.region + '</option>';
                                    });
                                    big_html += '</select>\n' +
                                        '                            </div>\n' +
                                        '                        </div>\n' +
                                        '\n' +
                                        '                        <div class="input_wrap column col_3">\n' +
                                        '                            <label class="small_faint">Target Audience</label>\n' +
                                        '                            <div class="select_wrap">\n' +
                                        '                                <select name="target_audiences[]" required>\n' +
                                        '                                    <option>Select</option>';
                                        $.each(target_audiences, function (index,value) {
                                            big_html += '<option value ="'+ value.id + '"> ' + value.audience + '</option>';
                                        });
                                        big_html += '</select>\n' +
                                            '                            </div>\n' +
                                            '                        </div>\n' +
                                            '\n' +
                                            '                        <div class="input_wrap column col_2">\n' +
                                            '                            <label class="small_faint">Minimum Age</label>\n' +
                                            '                            <input type="text" name="min_age[]" required placeholder="Enter Age">\n' +
                                            '                        </div>\n' +
                                            '\n' +
                                            '                        <div class="input_wrap column col_2">\n' +
                                            '                            <label class="small_faint">Maximum Age</label>\n' +
                                            '                            <input type="text" name="max_age[]" required placeholder="Enter Age">\n' +
                                            '                        </div>\n' +
                                            '                    </div><div class="align_right mb3">\n' +
                                            '                        <a href="" id="remove'+i+'" data-button_id="'+i+'" style="color:red" class="uppercased color_initial remove">Remove</a>\n' +
                                            '                    </div></div>';


                $("#dynamic_field").append(big_html);
            });

            $("body").delegate(".remove", "click", function () {
                event.preventDefault();
                var button_id = $(this).data("button_id");
                $(".remove_div"+button_id).remove();

            })


            $("body").delegate(".timepicker", "click", function() {
                $('.timepicker').wickedpicker({

                    // 12- or 24-hour format
                    twentyFour: true,

                    // CSS classes
                    upArrow: 'wickedpicker__controls__control-up',
                    downArrow: 'wickedpicker__controls__control-down',
                    close: 'wickedpicker__close',
                    hoverState: 'hover-state',

                    // title
                    title: 'Pick Time'

                });
            })
        })
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('new_frontend/css/wickedpicker.min.css') }}">
@stop
