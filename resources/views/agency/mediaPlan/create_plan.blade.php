@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
        @if(Session::get('broadcaster_id'))
            @include('partials.new-frontend.broadcaster.header')
            @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        @else
            @include('partials.new-frontend.agency.header')
        @endif

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create Media Plan</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_7 clearfix create_fields">

                <form method="POST" action="{{ route('agency.media_plan.suggestPlan') }}">
                    {{ csrf_field() }}
                    <div class="create_gauge">
                        <div class=""></div>
                    </div>

                    <!-- Media type & Gender -->
                    <div class="clearfix mb">
                        <div class="input_wrap column col_6">
                            <label class="small_faint">Media Type</label>

                            <div class="select_wrap{{ $errors->has('media_type') ? ' has-error' : '' }}">
                                <select name="media_type" id="media_type" required>
                                    <option>Select Media Type</option>
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "media_types")
                                            @foreach ($criteria->subCriterias as $media_types)
                                            <option value="{{ $media_types->name }}"
                                                    @if((Session::get('campaign_information')) != null)
                                                    @if($campaign_general_information->client === $client->id))
                                                    selected="selected"
                                                @endif
                                                @endif
                                            >{{ $media_types->name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>

                                @if($errors->has('client'))
                                    <strong>{{ $errors->first('media_type') }}</strong>
                                @endif
                            </div>
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint">Gender</label>

                            <div class="select_wrap {{ $errors->has('gender') ? ' has-error' : '' }}">
                                <select name="gender" id="gender">
                                    <option>Select Target Gender</option>
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "genders")
                                            @foreach ($criteria->subCriterias as $genders)
                                            <option value="{{ $genders->name }}"
                                                    @if((Session::get('campaign_information')) != null)
                                                    @if($campaign_general_information->client === $client->id))
                                                    selected="selected"
                                                @endif
                                                @endif
                                            >{{ $genders->name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                
                                @if($errors->has('gender'))
                                    <strong>{{ $errors->first('gender') }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- start time & end time -->
                    <div class="clearfix mb3">
                        <div class="input_wrap column col_6{{ $errors->has('start_date') ? ' has-error' : '' }}">
                            <label class="small_faint">Start Date</label>
                            <input type="text" required class="flatpickr" value="{{ old('start_date') }}" name="start_date" placeholder="Select Date">
                            @if($errors->has('start_date'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('start_date') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap column col_6{{ $errors->has('end_date') ? ' has-error' : '' }}">
                            <label class="small_faint">End Date</label>
                            <input type="text" required class="flatpickr" value="{{ old('end_date') }}" name="end_date" placeholder="Select Date">

                            @if($errors->has('end_date'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('end_date') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <!-- LSM & social classes -->
                    <div class="clearfix mb">
                        <div class="input_wrap column col_6">
                            <label class="small_faint">LSM</label>

                            <div class="select_wrap{{ $errors->has('lsm') ? ' has-error' : '' }}">
                                <select class="js-example-basic-multiple" name="lsm[]" id="lsm" multiple="multiple">
                                    <option>Select LSM</option>
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "living_standard_measures")
                                            @foreach ($criteria->subCriterias as $lsms)
                                            <option value="{{ $lsms->name }}"
                                                    @if((Session::get('campaign_information')) != null)
                                                    @if($campaign_general_information->client === $lsms->name))
                                                    selected="selected"
                                                @endif
                                                @endif
                                            >{{ $lsms->name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>

                                @if($errors->has('lsm'))
                                    <strong>{{ $errors->first('lsm') }}</strong>
                                @endif
                            </div>
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint">Social Class</label>

                            <div class="select_wrap {{ $errors->has('gender') ? ' has-error' : '' }}">
                                <select class="js-example-basic-multiple" name="social_class[]" id="social_class" multiple="multiple">
                                    <option>Select Social Class</option>
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "social_classes")
                                            @foreach ($criteria->subCriterias as $social_classes)
                                            <option value="{{ $social_classes->name }}"
                                                    @if((Session::get('campaign_information')) != null)
                                                    @if($campaign_general_information->client === $social_classes->name))
                                                    selected="selected"
                                                @endif
                                                @endif
                                            >{{ $social_classes->name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                
                                @if($errors->has('gender'))
                                    <strong>{{ $errors->first('gender') }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Target Age group -->
                    <div class="clearfix mb">
                        <div class="input_wrap column col_6{{  $errors->has('min_age') ? ' has-error' : '' }}">
                            <label class="small_faint">Min. Age</label>
                            <input type="number" name="age_groups[0][min]" placeholder="Minimum Age">

                            @if($errors->has('min_age'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('min_age') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap column col_6{{ $errors->has('max_age') ? ' has-error' : '' }}">
                            <label class="small_faint">Max. Age</label>
                            <input type="number" name="age_groups[0][max]" placeholder="Maximum Age">
                            @if($errors->has('max_age'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('max_age') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <!-- Regions -->
                    <div class="input_wrap">
                        <label class="small_faint">Region</label>

                        <div class="select_wrap{{ $errors->has('region') ? ' has-error' : '' }}">
                            <select class="js-example-basic-multiple" id="region" name="region[]" multiple="multiple">
                                <option value=""></option>
                                @foreach($criterias as $criteria)
                                    @if ($criteria->name == "regions")
                                        @foreach ($criteria->subCriterias as $regions)
                                        <option value="{{ $regions->name }}"
                                                @if((Session::get('campaign_information')) != null)
                                                @if($campaign_general_information->client === $regions->name))
                                                selected="selected"
                                            @endif
                                            @endif
                                        >{{ $regions->name }}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>

                            @if($errors->has('region'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('region') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <!-- States -->
                    <div class="input_wrap">
                        <label class="small_faint">State</label>

                        <div class="select_wrap{{ $errors->has('state') ? ' has-error' : '' }}">
                            <select class="js-example-basic-multiple" id="state" name="state[]" multiple="multiple">
                                <option value=""></option>
                                @foreach($criterias as $criteria)
                                    @if ($criteria->name == "states")
                                        @foreach ($criteria->subCriterias as $states)
                                        <option value="{{ $states->name }}"
                                                @if((Session::get('campaign_information')) != null)
                                                @if($campaign_general_information->client === $states->name))
                                                selected="selected"
                                            @endif
                                            @endif
                                        >{{ $states->name }}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>

                            @if($errors->has('state'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('state') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <!-- Agency commission -->
                    <div class="clearfix">
                        <div class="input_wrap column col_12{{ $errors->has('agency_commission') ? ' has-error' : '' }}">
                            <label class="small_faint">Agency Commission</label>
                            <input type="number" name="agency_commission" id="agency_commission" value="" placeholder="Enter Agency Commission">
                            @if($errors->has('agency_commission'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('agency_commission') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <!-- Campaign Name -->
                    <div class="clearfix mb3">
                        <div class="input_wrap column col_12{{ $errors->has('campaign_name') ? ' has-error' : '' }}">
                            <label class="small_faint">Campaign Name</label>
                            <input type="text" name="campaign_name" id="campaign_name" value="" placeholder="Enter Campaign Name">
                            @if($errors->has('campaign_name'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('campaign_name') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <div class="mb4 clearfix pt4 mb4">
                        <div class="column col_12 align_right">
                            <button type="submit"  class="btn uppercased ">Run Ratings <span class=""></span></button>
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
            $('#region').select2({
                placeholder: "Please select region"
            });

            $('#lsm').select2({
                placeholder: "Please select LSM"
            });

            $('#social_class').select2({
                placeholder: "Please select social class"
            });

            $('#state').select2({
                placeholder: "Please select state"
            });

            $(".checkbox_region").click( function (){
                var checkbox_region = $(this).data("region");
                var $inputs = $('.checkbox_region');
                if(checkbox_region === "naem6hqwjhjatseog8" && $(this).is(':checked')){
                    $(".checkbox_region").prop('checked', false);
                    $("#naem6hqwjhjatseog8").prop('checked', true);
                    $inputs.not(this).prop('disabled',true);
                }else{
                    $(".checkbox_region").prop('disabled', false);
                }
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

                                $("#industry").val(data.industry.name);
                                $("#sub_industry").val(data.sub_industry.name);
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


