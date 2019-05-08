@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Program</title>
@stop

@section('content')

    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')

    <div class="main_contain">
        {{--Header--}}
        @include('partials.new-frontend.broadcaster.header')
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create Program</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color pt load_stuff">
            <form action="{{ route('program.management.store') }}" method="post">
                {{ csrf_field() }}
                <div class="margin_center col_11 clearfix pt4 create_fields">

                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('program_name') ? ' has-error' : '' }}">
                            <label class="small_faint">Program</label>
                            <div class="">
                                <input type="text" name="program_name" required placeholder="Program Name">

                                @if($errors->has('program_name'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('program_name') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                        @if(Auth::user()->companies->count() == 1)
                            <div class="input_wrap column col_4">
                                <label class="small_faint">Rate Card</label>
                                <div class="select_wrap{{ $errors->has('rate_card') ? ' has-error' : '' }}">
                                    <select required name="rate_card">
                                        <option>Select Rate Card</option>
                                        @foreach($rate_cards as $rate_card)
                                            <option value="{{ $rate_card->id }}">{{ $rate_card->title }}</option>
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
                        @endif
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
                                <input type="text" name="start_date" class="flatpickr" required placeholder="Start Date">

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
                                <input type="text" name="end_date" class="flatpickr" required placeholder="End Date">

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
                        @if(count($companies) > 1)
                            <div class="input_wrap column col_4">
                                <label class="small_faint">Publisher</label>
                                <div class="select_wrap{{ $errors->has('company') ? ' has-error' : '' }}">
                                    <select required id="company" name="company">
                                        <option>Select Publisher</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
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
                            <div class="input_wrap column col_4">
                                <label class="small_faint">Rate Card</label>
                                <div class="select_wrap rate_card_select{{ $errors->has('rate_card') ? ' has-error' : '' }}">

                                    <select name="rate_card" id=""></select>
                                    @if($errors->has('rate_card'))
                                        <strong>
                                        <span class="help-block">
                                            {{ $errors->first('rate_card') }}
                                        </span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="company" value="{{ $companies[0]->id }}">
                        @endif
                    </div>

                    <p class='m-b'>Time Belt</p>
                    <hr>
                    <p><br></p>
                    <div class="clearfix m-b">
                        <!-- start -->
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
                        <!-- end -->
                    </div>
                    <!-- end -->


                    <!-- end -->

                    <div class="mb4 align_right pt">
                        <input type="submit" value="Create Program" class="btn uppercased mb4">
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

            $('body').delegate('#company','change', function(e){
                var company_id = $("#company").val();
                if(company_id != ''){
                    $(".load_stuff").css({
                        opacity: 0.3
                    });
                    var url = '/program-management/get-rate-card/'+company_id;
                    $.ajax({
                        url: url,
                        method: "GET",
                        data: {company_id: company_id},
                        success: function(data){
                            console.log(data);
                            if(data.rate_cards){
                                var big_html = '<select name="rate_card" id="rate_card">\n';
                                if(data.rate_cards != ''){
                                    big_html += '<option value="">Select Rate Card</option>';
                                    $.each(data.rate_cards, function (index, value) {
                                        big_html += '<option value="'+value.id+'">'+value.title+'</option>';
                                    });
                                    big_html += '</select>';
                                    $(".rate_card").hide();
                                    $(".rate_card_select").show();
                                    $(".rate_card_select").html(big_html);
                                    $(".load_stuff").css({
                                        opacity: 1
                                    });
                                }else{
                                    big_html += '<option value="">Please Select a Publisher</option></section>';
                                    $(".rate_card").hide();
                                    $(".rate_card_select").show();
                                    $(".rate_card_select").html(big_html);
                                    $(".load_stuff").css({
                                        opacity: 1
                                    });
                                }
                            }else{
                                $(".load_stuff").css({
                                    opacity: 1
                                });
                                toastr.error('An error occurred, please contact the administrator')
                            }

                        }
                    });
                }
            });
        })
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('new_frontend/css/wickedpicker.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="{{ asset('new_frontend/css/aria-accordion.css') }}" rel="stylesheet">
@stop
