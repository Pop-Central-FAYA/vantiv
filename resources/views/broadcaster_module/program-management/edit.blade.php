@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Edit Program</title>
@stop

@section('content')

    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')

    <div class="main_contain">
        {{--Header--}}
        @include('partials.new-frontend.broadcaster.header')
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Edit Program</h2>
            </div>
        </div>
        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color pt">
            <form action="{{ route('program.management.update', ['program_id' => $program->id]) }}" method="post">
                {{ csrf_field() }}
                <div class="margin_center col_11 clearfix pt4 create_fields">

                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('program_name') ? ' has-error' : '' }}">
                            <label class="small_faint">Program</label>
                            <div class="">
                                <input type="text" name="program_name" required value="{{ $program->name }}" placeholder="Program Name">

                                @if($errors->has('program_name'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('program_name') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                        <div class="input_wrap column col_4">
                            <label class="small_faint">Rate Card</label>
                            <div class="select_wrap{{ $errors->has('rate_card') ? ' has-error' : '' }}">
                                <select required name="rate_card">
                                    <option>Select Rate Card</option>
                                    @foreach($rate_cards as $rate_card)
                                        <option value="{{ $rate_card->id }}"
                                        @if($program->rate_card->id === $rate_card->id)
                                            selected
                                        @endif
                                        >{{ $rate_card->title }}</option>
                                    @endforeach
                                </select>

                                @if($errors->has('rate_card'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('rate_card') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                        {{--<div class="input_wrap column col_4">
                            <label class="small_faint">Program Vendor</label>
                            <div class="select_wrap{{ $errors->has('program_vendor') ? ' has-error' : '' }}">
                                <select required name="program_vendor">
                                    <option>Select Vendor</option>
                                </select>
                            </div>
                        </div>--}}
                    </div>

                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('start_date') ? ' has-error' : '' }}">
                            <label class="small_faint">Start Date</label>
                            <div class="">
                                <input type="text" name="start_date" class="flatpickr" value="{{ $program->start_date }}" required placeholder="Start Date">

                                @if($errors->has('start_date'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('start_date') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>

                        <div class="input_wrap column col_4 {{ $errors->has('end_date') ? ' has-error' : '' }}">
                            <label class="small_faint">End Date</label>
                            <div class="">
                                <input type="text" name="end_date" class="flatpickr" value="{{ $program->end_date }}" required placeholder="End Date">

                                @if($errors->has('end_date'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('end_date') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="clearfix mb3">
                        @if(Auth::user()->companies->count() > 1)
                            <div class="input_wrap column col_4">
                                <label class="small_faint">Publisher</label>
                                <div class="{{ $errors->has('company') ? ' has-error' : '' }}">
                                    <select required id="company" name="company">
                                        <option value="{{ $program->company->id }}">{{ $program->company->name }}</option>
                                    </select>

                                    @if($errors->has('company'))
                                        <strong>
                                            <span class="help-block">
                                                {{ $errors->first('company') }}
                                            </span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <p class='m-b'>Time Belt</p>
                    <hr>
                    <p><br></p>
                    <div class="clearfix m-b">
                        <!-- start -->
                        @foreach($time_belts as $time_belt)
                            <div class="clearfix m-b b remove_div_already_had_{{ $time_belt->id }}">
                                <div class="clearfix m-b">
                                    <div class="column col_2">
                                        <p>{{ ucfirst($time_belt->day) }}</p>
                                    </div>
                                    <input type="hidden" name="days[]" value="{{ $time_belt->day }}">
                                    <div class="input_wrap column col_3">
                                        <label class="small_faint">Start Time</label>
                                        <input type="text" id="timepicker" name="start_time[]" value="{{ explode('-',$time_belt->actual_time_picked)[0] }}" readonly/>
                                    </div>

                                    <div class="input_wrap column col_3">
                                        <label class="small_faint">End Time</label>
                                        <input type="text" id="timepicker" name="end_time[]" value="{{ explode('-',$time_belt->actual_time_picked)[1] }}" readonly/>
                                    </div>
                                    <div class="column col_2">
                                        <a href="" style="color:red" data-button_id="{{ $time_belt->id }}" class="uppercased color_initial remove_already">Remove</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    <!-- end -->
                    </div>
                    <hr>
                    <p><br></p>
                    <div class="clearfix m-b">
                        @foreach($days as $day)
                            <div class="clearfix m-b b" id="dynamic_field_{{ $day }}">
                                <div class="clearfix m-b">
                                    <div class="column col_2">
                                        <p>{{ ucfirst($day) }}</p>
                                    </div>
                                    <input type="hidden" name="days[]" value="{{ $day }}">
                                    <div class="input_wrap column col_3">
                                        <label class="small_faint">Start Time</label>
                                        <input type="text" id="timepicker" name="start_time[]" class="timepicker_{{ $day }}"/>
                                    </div>

                                    <div class="input_wrap column col_3">
                                        <label class="small_faint">End Time</label>
                                        <input type="text" id="timepicker" name="end_time[]" class="timepicker_{{ $day }}"/>
                                    </div>
                                    <div class="column col_2">
                                        <a href="" id="add_more_{{ $day }}" class="uppercased color_initial">Add More</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- end -->


                    <!-- end -->

                    <div class="mb4 align_right pt">
                        <input type="submit" value="Update Program" class="btn uppercased mb4">
                    </div>

                </div>
            </form>
        </div>
        <!-- main frame end -->
    </div><!-- main contain -->
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('new_frontend/js/wickedpicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('new_frontend/js/aria-accordion.js') }}"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script>
        <?php echo "var days =".json_encode($days).";\n"; ?>
        $(document).ready(function () {
            //flatpickr
            flatpickr(".flatpickr", {
                altInput: true,
            });

            $("body").delegate(".remove_already", "click", function () {
                event.preventDefault();
                var button_id = $(this).data("button_id");
                $(".remove_div_already_had_"+button_id).remove();

            });

            var i = $("select .b").length;
            var max = 12;
            $.each(days, function (index, value) {
                $("#add_more_"+value).click(function() {
                    event.preventDefault();
                    i++;
                    if (i >= max) {
                        return false;
                    }
                    var big_html = '';
                    big_html += '<div class="remove_div'+i+value+'"><div class="clearfix m-b">\n' +
                        '<div class="column col_2">\n' +
                        '                                        <p>"</p>\n' +
                        '                                    </div>'+
                        '                            <div class="input_wrap column col_3">\n' +
                        '                                <label class="small_faint">Start Time</label>\n' +
                        '                                <input type="text" id="timepicker" name="start_time[]" class="timepicker_'+value+'"/>\n' +
                        '                            </div>\n' +
                        '<input type="hidden" name="days[]" value="'+value+'">\n' +
                        '                            <div class="input_wrap column col_3">\n' +
                        '                                <label class="small_faint">End Time</label>\n' +
                        '                                <input type="text" id="timepicker" name="end_time[]" class="timepicker_'+value+'"/>\n' +
                        '                            </div>\n' +
                        '                           <div class="align_right mb3">\n' +
                        '                              <a href="" id="remove'+i+'" data-button_id="'+i+'" style="color:red" class="uppercased color_initial remove">Remove</a>\n' +
                        '                           </div></div>'+
                        '                        </div>';

                    $("#dynamic_field_"+value).append(big_html);

                });

                $("body").delegate(".remove", "click", function () {
                    event.preventDefault();
                    var button_id = $(this).data("button_id");
                    $(".remove_div"+button_id+value).remove();

                });

                $("body").delegate(".timepicker_"+value, "click", function() {
                    $('.timepicker_'+value).wickedpicker({

                        // 12- or 24-hour format
                        twentyFour: true,
                        now: "00:00",
                        // CSS classes
                        upArrow: 'wickedpicker__controls__control-up',
                        downArrow: 'wickedpicker__controls__control-down',
                        close: 'wickedpicker__close',
                        hoverState: 'hover-state',
                        minutesInterval: 15,

                        // title
                        title: 'Pick Time'

                    });
                })
            });
        })
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('new_frontend/css/wickedpicker.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="{{ asset('new_frontend/css/aria-accordion.css') }}" rel="stylesheet">
@stop
