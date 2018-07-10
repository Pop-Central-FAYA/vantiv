@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Campaign Step 1</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create New Campaign</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_7 clearfix pt4 create_fields">
                <div class="create_gauge clearfix">
                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress">
                        <span class="one_point"></span>
                    </div>
                </div>

                <form method="POST" action="{{ route('agency_campaign.store1') }}">
                    {{ csrf_field() }}
                    <div class="create_gauge">
                        <div class=""></div>
                    </div>
                    <div class="clearfix mb">
                        <div class="input_wrap column col_6">
                            <label class="small_faint">Clients</label>

                            <div class="select_wrap{{ $errors->has('clients') ? ' has-error' : '' }}">
                                <select name="clients" id="clients" required>
                                    <option>Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                        @if((Session::get('first_step')) != null)
                                            @if($first_step->clients === $client->id))
                                                selected="selected"
                                            @endif
                                        @endif
                                        >{{ $client->company_name }}</option>
                                    @endforeach
                                </select>

                                @if($errors->has('clients'))
                                    <strong>{{ $errors->first('clients') }}</strong>
                                @endif
                            </div>
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint">Brands</label>

                            <div class="select_wrap brand_select{{ $errors->has('brand') ? ' has-error' : '' }}">
                                @if((Session::get('first_step')) != null)
                                    <select name="brand" id="brand">
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                            @if($first_step->brand === $brand->id)
                                                selected
                                            @endif
                                            >{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @if($errors->has('brand'))
                                    <strong>{{ $errors->first('brand') }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="clearfix mb">
                        <div class="input_wrap column col_6{{ $errors->has('industry') ? ' has-error' : '' }}">
                            <label class="small_faint">Industry</label>
                            <input type="text" name="industry" id="industry" @if((Session::get('first_step')) != null) value="{{ $first_step->industry }}" @endif readonly placeholder="Industry...">

                            @if($errors->has('industry'))
                                <strong>{{ $errors->first('industry') }}</strong>
                            @endif
                        </div>

                        <div class="input_wrap column col_6{{ $errors->has('sub_industry') ? ' has-error' : '' }}">
                            <label class="small_faint">Sub Industry</label>
                            <input type="text" name="sub_industry" @if((Session::get('first_step')) != null) value="{{ $first_step->sub_industry }}" @endif id="sub_industry" readonly placeholder="Sub-Industry...">

                            @if($errors->has('sub_industry'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('sub_industry') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <div class="clearfix mb3">
                        <div class="input_wrap{{ $errors->has('campaign_name') ? ' has-error' : '' }}">
                            <label class="small_faint">Campaign Name</label>
                            <input type="text" required @if((Session::get('first_step')) != null) value="{{ $first_step->campaign_name }}" @endif name="campaign_name" placeholder="Campaign Name">

                            @if($errors->has('campaign_name'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('campaign_name') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap{{ $errors->has('campaign_budget') ? ' has-error' : '' }}">
                            <label class="small_faint">Campaign Budget</label>
                            <input type="number" required @if((Session::get('first_step')) != null) value="{{ $first_step->campaign_budget }}" @endif name="campaign_budget" placeholder="Campaign Budget">

                            @if($errors->has('campaign_budget'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('campaign_budget') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap{{ $errors->has('product') ? ' has-error' : '' }}">
                            <label class="small_faint">Product</label>
                            <input type="text" required name="product" @if((Session::get('first_step')) != null) value="{{ $first_step->product }}" @endif placeholder="Product">

                            @if($errors->has('product'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('product') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <div class="clearfix mb3">
                        <div class="input_wrap column col_6{{ $errors->has('start_date') ? ' has-error' : '' }}">
                            <label class="small_faint">Start Date</label>
                            <input type="text" required class="flatpickr" @if((Session::get('first_step')) != null) value="{{ $first_step->start_date }}" @endif name="start_date" placeholder="Select Date">
                            @if($errors->has('start_date'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('start_date') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap column col_6{{ $errors->has('end_date') ? ' has-error' : '' }}">
                            <label class="small_faint">End Date</label>
                            <input type="text" required class="flatpickr" name="end_date" @if((Session::get('first_step')) != null) value="{{ $first_step->end_date }}" @endif placeholder="Select Date">

                            @if($errors->has('end_date'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('end_date') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <p class="mb">Media Type</p>
                    <div class="create_check clearfix mb3{{ $errors->has('channel') ? ' has-error' : '' }}">
                        <ul>
                            @foreach($channels as $channel)
                                <li class="col_4 column m-b">
                                    <input name="channel[]" value="{{ $channel->id }}"
                                           @if((Session::get('first_step')) != null)
                                                @foreach($first_step->channel as $checked_channel)
                                                    @if($checked_channel === $channel->id)
                                                        checked
                                                    @endif
                                                @endforeach
                                           @endif
                                           type="checkbox" id='{{ $channel->id }}'>
                                    <label for="{{ $channel->id }}">{{ $channel->channel }}</label>
                                </li>
                            @endforeach
                        </ul>

                        @if($errors->has('channel'))
                            <strong>
                                <span class="help-block">{{ $errors->first('channel') }}</span>
                            </strong>
                        @endif
                    </div>

                    <p class='mb'>Day Parts</p>

                    <div class="create_check clearfix mb3{{ $errors->has('dayparts') ? ' has-error' : '' }}">
                        <ul>
                            @foreach($day_parts as $day_part)
                                <li class="col_4 column m-b">
                                    <input name="dayparts[]" value="{{ $day_part->id }}"
                                           @if((Session::get('first_step')) != null)
                                               @foreach($first_step->dayparts as $checked_dayparts)
                                                   @if($checked_dayparts === $day_part->id)
                                                        checked
                                                   @endif
                                               @endforeach
                                           @endif
                                           type="checkbox" id='{{ $day_part->id }}'>
                                    <label for="{{ $day_part->id }}">{{ $day_part->day_parts }}</label>
                                </li>
                            @endforeach
                        </ul>
                        @if($errors->has('dayparts'))
                            <strong>
                                <span class="help-block">{{ $errors->first('dayparts') }}</span>
                            </strong>
                        @endif
                    </div>

                    <div class="input_wrap">
                        <label class="small_faint">Target Audience</label>

                        <div class="select_wrap{{ $errors->has('target_audience') ? ' has-error' : '' }}">
                            <select class="js-example-basic-multiple" id="target_aud" name="target_audience[]" multiple="multiple">
                                <option value=""></option>
                                @foreach($targets as $target)
                                    <option value="{{ $target->id }}"
                                    @if((Session::get('first_step')) != null)
                                        @foreach($first_step->target_audience as $selected_audience)
                                            @if($selected_audience === $target->id)
                                                selected
                                            @endif
                                        @endforeach
                                    @endif
                                    >{{ $target->audience }}</option>
                                @endforeach
                            </select>

                            @if($errors->has('target_audience'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('target_audience') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <div class="clearfix mb">
                        <div class="input_wrap column col_6{{  $errors->has('min_age') ? ' has-error' : '' }}">
                            <label class="small_faint">Min. Age</label>
                            <input type="number" required name="min_age" @if((Session::get('first_step')) != null) value="{{ $first_step->min_age }}" @endif placeholder="Minimum Age">

                            @if($errors->has('min_age'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('min_age') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap column col_6{{ $errors->has('max_age') ? ' has-error' : '' }}">
                            <label class="small_faint">Max. Age</label>
                            <input type="number" required name="max_age" @if((Session::get('first_step')) != null) value="{{ $first_step->max_age }}" @endif placeholder="Maximum Age">
                            @if($errors->has('max_age'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('max_age') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>


                    <p class='mb'>Region</p>

                    <div class="create_check clearfix mb4{{ $errors->has('region') ? ' has-error' : '' }}">
                        <ul>
                            @foreach($regions as $region)
                                <li class="col_4 column m-b">
                                    <input name="region[]" value="{{ $region->id }}"
                                           @if((Session::get('first_step')) != null)
                                               @foreach($first_step->region as $checked_region)
                                                   @if($checked_region === $region->id)
                                                       checked
                                                   @endif
                                               @endforeach
                                           @endif
                                           type="checkbox" id='{{ $region->id }}'>
                                    <label for="{{ $region->id }}">{{ $region->region }}</label>
                                </li>
                            @endforeach
                        </ul>

                        @if($errors->has('region'))
                            <strong>
                                <span class="help-block">{{ $errors->first('region') }}</span>
                            </strong>
                        @endif
                    </div>


                    <div class="mb4 align_right pt">
                        {{--<button class="btn uppercased mb4" type="submit">Proceed</button>--}}
                        <input type="submit" id="button" value="Proceed" class="btn uppercased mb4">
                    </div>

                </form>
            </div>
        </div>
        <!-- main frame end -->


    </div>
@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            //flatpickr
            flatpickr(".flatpickr", {
                altInput: true,
            });
            //select for target audience
            $('.js-example-basic-multiple').select2();
            //placeholder for target audienct
            $('#target_aud').select2({
                placeholder: "Please select Target Audience"
            });
            // fetch all brands when a clientSis selected
            $('body').delegate('#clients','change', function(e){
                var clients = $("#clients").val();
                if(clients != ''){
                    $(".load_stuff").css({
                        opacity: 0.3
                    });
                    $("#industry").val('');
                    $("#sub_industry").val('');
                    var url = '/client/get-brands/'+clients;
                    $.ajax({
                        url: url,
                        method: "GET",
                        data: {clients: clients},
                        success: function(data){
                            if(data.brands){
                                var big_html = '<select name="brand" id="brand">\n';
                                if(data.brands != ''){
                                    big_html += '<option value="">Select Brand</option>';
                                    $.each(data.brands, function (index, value) {
                                        big_html += '<option value="'+value.id+'">'+value.name+'</option>';
                                    });
                                    big_html += '</select>';
                                    $(".brand_hide").hide();
                                    $(".brand_select").show();
                                    $(".brand_select").html(big_html);
                                    $(".load_stuff").css({
                                        opacity: 1
                                    });
                                }else{
                                    big_html += '<option value="">Please Select a Client</option></section>';
                                    $(".brand_hide").hide();
                                    $(".brand_select").show();
                                    $(".brand_select").html(big_html);
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
                }else{
                    $("#industry").val('');
                    $("#sub_industry").val('');
                }
            });

            //fetch all industry and sub-industry attached to a brand
            $('body').delegate('#brand','change', function(e) {
                var brand = $("#brand").val();
                if (brand != '') {
                    $(".load_stuff").css({
                        opacity: 0.5
                    });
                    $('.next').attr("disabled", true);
                    var url = '/brand/get-industry';
                    $.ajax({
                        url: url,
                        method: "GET",
                        data: {brand: brand},
                        success: function (data) {
                            if (data.error === 'error') {
                                $(".load_stuff").css({
                                    opacity: 1
                                });
                                toastr.error('An error occured, please contact the administratot ')
                            } else {
                                $(".load_stuff").css({
                                    opacity: 1
                                });

                                $("#industry").val(data.industry[0].name);
                                $("#sub_industry").val(data.sub_industry[0].name);
                            }

                        }
                    });
                } else {
                    $("#industry").val('');
                    $("#sub_industry").val('');
                }
            });
        });
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop



{{--@extends('layouts.new_app')--}}
{{--@section('title')--}}
    {{--<title>Agency | Create Campaigns</title>--}}
{{--@stop--}}
{{--@section('content')--}}

    {{--<div class="main-section">--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-12 heading-main">--}}
                    {{--<h1>Create Campaigns</h1>--}}
                    {{--<ul>--}}
                        {{--<li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Agency</a></li>--}}
                        {{--<li><a href="{{ route('agency.campaign.all') }}">All Campaign</a></li>--}}
                    {{--</ul>--}}
                {{--</div>--}}
                {{--<div class="Create-campaign changing">--}}
                    {{--<form class="campform" method="POST" action="{{ route('agency_campaign.store1', ['id' => $id]) }}">--}}
                        {{--{{ csrf_field() }}--}}
                        {{--<div class="col-12 ">--}}
                            {{--<h2>Campaign Details</h2>--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--<p><br></p>--}}
                            {{--<p><br></p>--}}
                            {{--<div class="col-12 form-inner">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label class="col-md-2">Campaign Name:</label>--}}
                                    {{--<div class="col-md-6">--}}
                                        {{--<input type="text" name="name" class="form-control" value="{{ isset(((object) $step1)->name) ? ((object) $step1)->name: "" }}" required  placeholder="Campaign Name">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--<div class="col-12 form-inner">--}}
                                {{--<div class="form-group">--}}
                                    {{--<label class="col-md-2">Product:</label>--}}
                                    {{--<div class="col-md-6">--}}
                                        {{--<input type="text" class="form-control" name="product" value="{{ isset(((object) $step1)->product) ? ((object) $step1)->product : "" }}" required placeholder="Product">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--@if(isset($step1))--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label class="col-md-2">Brands:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<select name="brand" class="Role form-control">--}}
                                                {{--@foreach($brands as $b)--}}
                                                    {{--<option value="{{ $b->id }}"--}}
                                                        {{--@if($step1->brand === $b->id)--}}
                                                        {{--selected--}}
                                                        {{--@endif--}}
                                                    {{-->{{ $b->name }}</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@else--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label class="col-md-2">Brands:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<select name="brand" id="brand" class="Role form-control">--}}
                                                {{--<option value="">Select Brand</option>--}}
                                                {{--@foreach($brands as $b)--}}
                                                    {{--<option value="{{ $b->id }}">{{ $b->name }}</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--@if(isset($step1))--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label class="col-md-2">Industry:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<input type="text" name="industry" id="industry" required readonly class="form-control" @foreach($industry as $ind) @if($step1->industry === $ind->name) value="{{ $ind->name }}" @endif @endforeach>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@else--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label class="col-md-2">Industry:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<input type="text" name="industry" class="form-control" readonly id="industry">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--@if(isset($step1))--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label class="col-md-2">Sub Industry:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<input type="text" name="sub_industry" id="sub_industry" class="form-control" required readonly @foreach($sub_industries as $sub_industry) @if($step1->sub_industry === $sub_industry->name) value="{{ $sub_industry->name }}" @endif @endforeach>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@else--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label class="col-md-2">Sub Industry:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<input type="text" name="sub_industry" class="form-control" readonly id="sub_industry">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--@if(isset($step1))--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="targer_audience" class="col-md-2">Target Audience:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<select name="target_audience" class="Role form-control">--}}
                                                {{--@foreach($target as $target_audiences)--}}
                                                    {{--<option value="{{ $target_audiences->id }}"--}}
                                                        {{--@if($step1->target_audience === $target_audiences->id)--}}
                                                            {{--selected--}}
                                                        {{--@endif--}}
                                                    {{-->{{ $target_audiences->audience }}</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@else--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="targer_audience" class="col-md-2">Target Audience:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<select name="target_audience" class="Role form-control">--}}
                                                {{--@foreach($target as $target_audiences)--}}
                                                    {{--<option value="{{ $target_audiences->id }}">{{ $target_audiences->audience }}</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--@if(isset($step1))--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="channel" class="col-md-2">Channel:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<select name="channel" class="Role form-control">--}}
                                                {{--@foreach($chanel as $chanels)--}}
                                                    {{--<option value="{{ $chanels->id }}"--}}
                                                            {{--@if($step1->channel === $chanels->id)--}}
                                                            {{--selected--}}
                                                            {{--@endif--}}
                                                    {{-->{{ $chanels->channel }}</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@else--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="channel" class="col-md-2">Channel:</label>--}}
                                        {{--<div class="col-md-4">--}}
                                            {{--<select name="channel" class="Role form-control">--}}
                                                {{--@foreach($chanel as $chanels)--}}
                                                    {{--<option value="{{ $chanels->id }}">{{ $chanels->channel }}</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--<div class="col-12 form-inner">--}}
                                {{--<div class="form-group">--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-md-6">--}}
                                            {{--<label for="start_date" class="col-md-2">Start Date:</label>--}}
                                            {{--<div class="col-md-6">--}}
                                                {{--<input type="text" class="form-control flatpickr" value="{{ isset(((object) $step1)->start_date) ?((object) $step1)->start_date : "" }}" required name="start_date"  id="datepicker" placeholder="Start-Date">--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="col-md-6">--}}
                                            {{--<label for="stop_date" class="col-md-2">Stop Date:</label>--}}
                                            {{--<div class="col-md-6">--}}
                                                {{--<input type="text" class="form-control flatpickr" value="{{ isset(((object) $step1)->end_date) ? ((object) $step1)->end_date : "" }}" required name="end_date" id="datepicker1" placeholder="Stop-Date">--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--<div class="col-12 form-inner">--}}
                                {{--<div class="form-group">--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-md-6">--}}
                                            {{--<label for="min_age" class="col-md-2">Min Age:</label>--}}
                                            {{--<div class="col-md-6">--}}
                                                {{--<input type="number" name="min_age" value="{{ isset(((object) $step1)->min_age) ?((object) $step1)->min_age : "" }}" required class="form-control">--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="col-md-6">--}}
                                            {{--<label for="max_age" class="col-md-2">Max Age:</label>--}}
                                            {{--<div class="col-md-6">--}}
                                                {{--<input type="number" class="form-control" name="max_age" required value="{{ isset(((object) $step1)->max_age) ?((object) $step1)->max_age : "" }}">--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--@if(isset($step1))--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="day_parts" class="col-md-2">Day Parts:</label>--}}
                                        {{--<div class="col-md-8">--}}
                                            {{--All--}}
                                            {{--<p>--}}
                                                {{--<input type="checkbox" id="checkAll"--}}
                                                       {{--value="" class="minimal-red"  /></p>--}}
                                            {{--@foreach($day_part as $day_parts)--}}
                                                {{--<input type="checkbox" class="checked_this" name="dayparts[]"--}}
                                                       {{--@foreach($step1->dayparts as $daypart)--}}
                                                       {{--@if($daypart === $day_parts->id)--}}
                                                       {{--checked--}}
                                                       {{--@endif--}}
                                                       {{--@endforeach--}}
                                                       {{--value="{{ $day_parts->id }}">{{ $day_parts->day_parts }}--}}
                                            {{--@endforeach--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@else--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="day_parts" class="col-md-2">Day Parts:</label>--}}
                                        {{--<div class="col-md-8">--}}
                                            {{--All--}}
                                            {{--<p>--}}
                                            {{--<input type="checkbox" id="checkAll"--}}
                                                   {{--value="" class="minimal-red"  /></p>--}}
                                            {{--@foreach($day_part as $day_parts)--}}
                                                {{--<input type="checkbox" name="dayparts[]" class="checked_this" value="{{ $day_parts->id }}">{{ $day_parts->day_parts }}--}}
                                            {{--@endforeach--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                            {{--@if(isset($step1))--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="region" class="col-md-2">Region:</label>--}}
                                        {{--<div class="col-md-8">--}}
                                            {{--<input type="checkbox" name="region[]"--}}
                                                   {{--@foreach($step1->region as $regions)--}}
                                                       {{--@if($regions === $region[0]->id)--}}
                                                            {{--checked--}}
                                                       {{--@endif--}}
                                                   {{--@endforeach--}}
                                                   {{--value="{{ $region[0]->id }}">{{ $region[0]->region }}--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@else--}}
                                {{--<div class="col-12 form-inner">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="region" class="col-md-2">Region:</label>--}}
                                        {{--<div class="col-md-8">--}}
                                            {{--<input type="checkbox" name="region[]" value="{{ $region[0]->id }}">{{ $region[0]->region }}--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                            {{--<hr>--}}
                            {{--<p><br></p>--}}
                                {{--<div class="input-group">--}}
                                    {{--<a href="{{ route('clients.list') }}" style="background: #00c4ca" class="btn btn-danger btn-lg"><< Back</a>--}}
                                    {{--<input type="Submit" style="background: #00c4ca" class="btn btn-danger btn-lg next" name="Submit" value="Next >>">--}}
                                {{--</div>--}}

                            {{--</div>--}}

                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}


{{--@stop--}}
{{--@section('scripts')--}}
    {{--<!-- Select2 -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/select2/select2.full.min.js') }}"></script>--}}
    {{--<!-- InputMask -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.js') }}"></script>--}}
    {{--<script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>--}}
    {{--<script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>--}}
    {{--<!-- date-range-picker -->--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>--}}
    {{--<script src="{{ asset('agency_asset/plugins/daterangepicker/daterangepicker.js') }}"></script>--}}
    {{--<!-- bootstrap datepicker -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>--}}
    {{--<!-- bootstrap color picker -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>--}}
    {{--<!-- bootstrap time picker -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>--}}
    {{--<!-- SlimScroll 1.3.0 -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>--}}
    {{--<!-- iCheck 1.0.1 -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/iCheck/icheck.min.js') }}"></script>--}}
    {{--<!-- FastClick -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/fastclick/fastclick.js') }}"></script>--}}
    {{--<!-- AdminLTE App -->--}}
    {{--<script src="{{ asset('agency_asset/dist/js/app.min.js') }}"></script>--}}

    {{--<!-- DataTables -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>--}}
    {{--<script src="{{ asset('agency_asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>--}}
    {{--<script src="https://unpkg.com/flatpickr"></script>--}}

    {{--<script>--}}
        {{--flatpickr(".flatpickr", {--}}
            {{--altInput: true,--}}
        {{--});--}}
    {{--</script>--}}
    {{--<script>--}}

        {{--$(document).ready(function(){--}}
            {{--$("#checkAll").click(function () {--}}
                {{--$('input:checkbox.checked_this').not(this).prop('checked', this.checked);--}}
            {{--});--}}

            {{--$("#txtFromDate").datepicker({--}}
                {{--numberOfMonths: 2,--}}
                {{--onSelect: function (selected) {--}}
                    {{--$("#txtToDate").datepicker("option", "minDate", selected)--}}
                {{--}--}}
            {{--});--}}

            {{--$("#txtToDate").datepicker({--}}
                {{--numberOfMonths: 2,--}}
                {{--onSelect: function(selected) {--}}
                    {{--$("#txtFromDate").datepicker("option","maxDate", selected)--}}
                {{--}--}}
            {{--});--}}

        {{--});--}}
    {{--</script>--}}
    {{--<script>--}}
        {{--$(document).ready(function () {--}}
            {{--$("#checkAll").click(function () {--}}
                {{--$('input:checkbox.checked_this').not(this).prop('checked', this.checked);--}}
            {{--});--}}

            {{--// $("#state").change(function() {--}}
            {{--$('#brand').on('change', function(e){--}}
                {{--var brand = $("#brand").val();--}}
                {{--if(brand != ''){--}}
                    {{--$(".changing").css({--}}
                        {{--opacity: 0.5--}}
                    {{--});--}}
                    {{--$('.next').attr("disabled", true);--}}
                    {{--var url = '/brand/get-industry';--}}
                    {{--$.ajax({--}}
                        {{--url: url,--}}
                        {{--method: "GET",--}}
                        {{--data: {brand: brand},--}}
                        {{--success: function(data){--}}
                            {{--if(data.error === 'error'){--}}
                                {{--$(".changing").css({--}}
                                    {{--opacity: 1--}}
                                {{--});--}}
                                {{--$('.next').attr("disabled", false);--}}
                            {{--}else{--}}
                                {{--$(".changing").css({--}}
                                    {{--opacity: 1--}}
                                {{--});--}}
                                {{--$('.next').attr("disabled", false);--}}

                                {{--$("#industry").val(data.industry[0].name);--}}
                                {{--$("#sub_industry").val(data.sub_industry[0].name);--}}
                            {{--}--}}

                        {{--}--}}
                    {{--});--}}
                {{--}else{--}}
                    {{--$("#industry").val('');--}}
                    {{--$("#sub_industry").val('');--}}
                {{--}--}}
            {{--});--}}
        {{--});--}}

    {{--</script>--}}

{{--@stop--}}
{{--@section('styles')--}}
    {{--<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">--}}
{{--@stop--}}

