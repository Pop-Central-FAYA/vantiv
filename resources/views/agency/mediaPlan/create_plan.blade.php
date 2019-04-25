@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain load_this_div">
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
        <div class="the_frame clearfix mb border_top_color">
            <div class="margin_center col_7 clearfix create_fields">
                <form method="POST" action="" id="criteria_form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="create_gauge">
                        <div class=""></div>
                    </div>

                    <!-- Media type & Gender -->
                    <div class="clearfix mb">
                        <div class="input_wrap column col_6">
                            <label class="small_faint">Media Type</label>

                            <div class="select_wrap{{ $errors->has('media_type') ? ' has-error' : '' }}">
                                <select name="media_type" id="media_type" required>
                                    <!-- <option>Select Media Type</option> -->
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "media_types")
                                            @foreach ($criteria->subCriterias as $media_types)
                                            @if ($media_types->name == "Tv")
                                                <option value="{{ $media_types->name }}" selected
                                            >{{ $media_types->name }}</option>
                                            @else
                                                <option value="{{ $media_types->name }}" disabled
                                            >{{ $media_types->name }}</option>
                                            @endif
                                            
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>

                                @if($errors->has('media_type'))
                                    <strong>{{ $errors->first('media_type') }}</strong>
                                @endif
                            </div>
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint">Gender</label>

                            <div class="select_wrap {{ $errors->has('gender') ? ' has-error' : '' }}">
                                <select class="js-example-basic-multiple all" name="gender[]" id="gender" multiple="multiple" onchange="selectAll('gender')">
                                <option value="all">Both</option>
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "genders")
                                            @foreach ($criteria->subCriterias as $genders)
                                            <option value="{{ $genders->name }}"
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
                    <div class="clearfix mb">
                        <div class="input_wrap column col_6{{ $errors->has('start_date') ? ' has-error' : '' }}">
                            <label class="small_faint">Start Date</label>
                            <input style="display: inline-block;width: 100%;" type="text" id="start_date" required class="flatpickr" value="{{ old('start_date') }}" name="start_date" placeholder="Select Date">
                            @if($errors->has('start_date'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('start_date') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap column col_6{{ $errors->has('end_date') ? ' has-error' : '' }}">
                            <label class="small_faint">End Date</label>
                            <input style="display: inline-block;width: 100%;" type="text" required class="flatpickr" id="end_date" value="{{ old('end_date') }}" name="end_date" placeholder="Select Date">

                            @if($errors->has('end_date'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('end_date') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <!-- Target Age group -->
                    <div class="clearfix mb">
                        <div class="input_wrap column col_6{{  $errors->has('age_groups') ? ' has-error' : '' }}">
                            <label class="small_faint">Min. Age</label>
                            <input style="display: inline-block;width: 100%;" type="number" name="age_groups[0][min]" placeholder="Minimum Age">

                            @if($errors->has('age_groups'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('age_groups') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap column col_6{{ $errors->has('age_groups') ? ' has-error' : '' }}">
                            <label class="small_faint">Max. Age</label>
                            <input style="display: inline-block;width: 100%;" type="number" name="age_groups[0][max]" placeholder="Maximum Age">
                            @if($errors->has('age_groups'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('age_groups') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <!-- LSM & social classes -->
                    <div class="clearfix mb">
                        <!-- <div class="input_wrap column col_6">
                            <label class="small_faint">LSM</label>

                            <div class="select_wrap{{ $errors->has('lsm') ? ' has-error' : '' }}">
                                <select class="js-example-basic-multiple" name="lsm[]" id="lsm" multiple="multiple">
                                    <option>Select LSM</option>
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "living_standard_measures")
                                            @foreach ($criteria->subCriterias as $lsms)
                                            <option value="{{ $lsms->name }}"

                                            >{{ $lsms->name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>

                                @if($errors->has('lsm'))
                                    <strong>{{ $errors->first('lsm') }}</strong>
                                @endif
                            </div>
                        </div> -->

                        <div class="input_wrap column col_12">
                            <label class="small_faint">Social Class</label>

                            <div class="select_wrap {{ $errors->has('social_class') ? ' has-error' : '' }}">
                                <select class="js-example-basic-multiple all" name="social_class[]" id="social_class" multiple="multiple" onchange="selectAll('social_class')"  >
                                <option value="all">All</option>
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "social_classes")
                                            @foreach ($criteria->subCriterias as $social_classes)
                                            <option value="{{ $social_classes->name }}"
                                            >{{ $social_classes->name }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                
                                @if($errors->has('social_class'))
                                    <strong>{{ $errors->first('social_class') }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- States -->
                    <div class="clearfix mb">
                        <div class="input_wrap">
                            <label class="small_faint">State</label> 

                            <div class="select_wrap{{ $errors->has('state') ? ' has-error' : '' }}">
                                <select class="js-example-basic-multiple all" id="state" name="state[]" multiple="multiple"  onchange="selectAll('state')" >
                                    <option value="all">All</option>
                                    @foreach($criterias as $criteria)
                                        @if ($criteria->name == "states")
                                            @foreach ($criteria->subCriterias as $states)
                                            <option value="{{ $states->name }}"
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
                    </div>

                    <!-- Regions -->
                    <!-- <div class="input_wrap">
                        <label class="small_faint">Region</label>

                        <div class="select_wrap{{ $errors->has('region') ? ' has-error' : '' }}">
                            <select class="js-example-basic-multiple" id="region" name="region[]" multiple="multiple">
                                <option value=""></option>
                                @foreach($criterias as $criteria)
                                    @if ($criteria->name == "regions")
                                        @foreach ($criteria->subCriterias as $regions)
                                        <option value="{{ $regions->name }}"

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
                    </div> -->

                    <!-- Agency commission -->
                    <div class="clearfix mb">
                        <div class="input_wrap column col_12{{ $errors->has('agency_commission') ? ' has-error' : '' }}">
                            <label class="small_faint">Agency Commission</label>
                            <input style="display: inline-block;width: 100%;" type="number" name="agency_commission" id="agency_commission" value="{{ old('agency_commission') }}" placeholder="Enter Agency Commission">
                            @if($errors->has('agency_commission'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('agency_commission') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <!-- Campaign Name -->
                    <div class="clearfix mb">
                        <div class="input_wrap column col_12{{ $errors->has('campaign_name') ? ' has-error' : '' }}">
                            <label class="small_faint">Campaign Name</label>
                            <input style="display: inline-block;width: 100%;" type="text" name="campaign_name" id="campaign_name" value="{{ old('campaign_name') }}" placeholder="Enter Campaign Name">
                            @if($errors->has('campaign_name'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('campaign_name') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <div class="mb4 clearfix pt4 mb4">
                        <div class="column col_12 align_right">
                            <button type="submit"  class="btn uppercased button_create">Run Ratings <span class=""></span></button>
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



     function selectAll(container)
        {
                    var selectedItem = $("#"+ container).val();
                    if(selectedItem == "all" ){
                          $("#"+ container +"> option").prop("selected","selected");
                          $("#"+container +" option[value='all']").attr("selected", false);
                         $("#"+ container).trigger("change");
                     }
        }

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

            $('#gender').select2({
                placeholder: "Please select gender"
            });

            $('#state').select2({
                placeholder: "Please select state"
            });

            $("#criteria_form").on('submit', function(e) {
                event.preventDefault(e);
                $('.load_this_div').css({
                    opacity : 0.2
                });
                $('a').css('pointer-events','none');
                $('.button_create').hide();
                var start_date = $("#start_date").val();
                var end_date = $("#end_date").val();
                var campaign_name = $("#campaign_name").val();
                if(start_date === '' && end_date === ''){
                    toastr.error('Start and end dates are required.');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                if(new Date(start_date) < new Date() || new Date(end_date) < new Date()){
                    toastr.error('Start or end date needs to be greater than the current date');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                if(campaign_name === ""){
                    toastr.error('Campaign name is required');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                if(new Date(start_date) > new Date(end_date)){
                    toastr.error('Start date cannot be greater than end date');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                var formdata = $("#criteria_form").serialize();
                var weHaveSuccess = false;
                
                $.ajax({
                    cache: false,
                    type: "POST",
                    url: '/agency/media-plan/create-plan',
                    dataType: 'json',
                    data: formdata,
                    beforeSend: function(data) {
                        // run toast showing progress
                        toastr_options = {
                            "progressBar": true,
                            // "showDuration": "300",
                            "preventDuplicates": true,
                            "tapToDismiss": false,
                            "hideDuration": "1",
                            "timeOut": "300000000"
                        };
                        msg = "Generating ratings, please wait"
                        toastr.info(msg, null, toastr_options)
                    },
                    success: function (data) {
                        toastr.clear();
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            location.href = '/agency/media-plan/customise/' + data.redirect_url;
                        } else {
                            toastr.error(data.message);
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }
                        weHaveSuccess = true;
                    },
                    error : function (xhr) {
                        toastr.clear();
                        if(xhr.status === 500){
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }else if(xhr.status === 503){
                            toastr.error('The request took longer than expected, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }else{
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }
                    }
                })
            });
        });
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop


