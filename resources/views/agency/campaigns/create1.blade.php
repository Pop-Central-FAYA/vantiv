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
                                <select name="client" id="clients" required>
                                    <option>Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                        @if((Session::get('first_step')) != null)
                                            @if($first_step->client === $client->id))
                                                selected="selected"
                                            @endif
                                        @endif
                                        >{{ $client->company_name }}</option>
                                    @endforeach
                                </select>

                                @if($errors->has('client'))
                                    <strong>{{ $errors->first('client') }}</strong>
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
                                @else
                                    <select name="brand" id=""></select>
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
                            <select class="js-example-basic-multiple" id="target_aud" name="target_audience[]" multiple="multiple" >
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
                    <div class="mb4 clearfix pt4 mb4">
                        <div class="column col_6">
                            <a  class="btn uppercased _proceed modal_click">Upload Media Plan </a>
                        </div>

                        <div class="column col_6 align_right">
                            <button type="submit"  class="btn uppercased ">Proceed <span class=""></span></button>
                        </div>
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


