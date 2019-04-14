@extends('layouts.faya_app')

@section('title')
<title>FAYA | Create Media Plan</title>
@stop

@section('extra-meta')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
            {{-- <h2 class="sub_header">Selected stations & programs</h2> --}}
            {{-- <p><a href="#ex1" rel="modal:open">Open Modal</a></p> --}}
        </div>
    </div>
    <div>
        @foreach($default_material_length as $duration)
            <div class="the_frame client_dets mb4 load_this_div">

                <div class="filters border_bottom clearfix">
                    <div class="column col_8 p-t">
                        <p class="uppercased weight_medium revealer" id="{{ $duration }}SecRevealer">{{ $duration }} sec </p> <p class="update_{{ $duration }}">Sub Total :</p>
                    </div>
                    <div class="column col_4 p-t right">
                        <div class="select_wrap">
                            <select name="month" id="month">
                                <option>Select Month</option>
                                <option value="">January</option>
                                <option value="">February</option>
                                <option value="">March</option>
                                <option value="">April</option>
                                <option value="">May</option>
                                <option value="">June</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- campaigns table -->
                <div id="table-scroll" class="table-scroll_{{ $duration }} refresh_data_{{ $duration }}">
                    <div class="accordion-group table-wrap" id="{{ $duration }}SecBox">
                        <table class="display default_mpo filter_mpo" id="default_mpo_table_{{ $duration }}">
                            <thead>
                            <tr>
                                <th class="fixed-side">Station</th>
                                <th class="fixed-side">Day</th>
                                <th class="fixed-side">Time Belt</th>
                                <th class="fixed-side" id="last-fixed">Programme</th>
                                <th class="fixed-side">Unit Rate</th>
                                <th class="fixed-side">Volume Disc</th>
                                <th class="fixed-side">Total Exposure</th>
                                @for ($i = 0; $i < count($fayaFound['labeldates']); $i++)
                                    <th>{{ $fayaFound['labeldates'][$i] }}</th>
                                @endfor

                            </tr>
                            </thead>
                            <tbody>

                            @foreach($fayaFound['programs_stations'] as $value)
                                <tr class="{{ $value->program}}">
                                    <td id="btn" class="fixed-side">{{ $value->station }}</td>
                                    <td id="btn" class="fixed-side">{{ $value->day }}</td>
                                    <td id="btn" class="fixed-side"> {{ substr($value->start_time,0,5) }} {{ substr($value->end_time,0,5) }}</td>
                                    <td id="btn" class="fixed-side update_program_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->day.'_'.$value->station.'_'.$value->start_time)) }}">
                                        <a href="#program_modal_{{ $duration }}{{ $value->id }}" class="modal_click update_program_a_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->day.'_'.$value->station.'_'.$value->start_time)) }}">{{ $value->program }}</a>
                                    </td>
                                    @if($value->duration_lists != "[null]")
                                        <td>
                                            <input type="number" readonly
                                                   @foreach(json_decode($value->duration_lists) as $key => $duration_list)
                                                   @if($duration_list == $duration)
                                                   value="{{ json_decode($value->rate_lists)[$key] }}"
                                                   @endif
                                                   @endforeach
                                                   class="update_rating_class_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->day.'_'.$value->station.'_'.$value->start_time)) }}"
                                                   id="ur{{ $duration }}{{$value->id}}" data_12="{{ $value->id}}"
                                                   data_11="60" data_10="" data_9="">
                                        </td>
                                    @else
                                        <td>
                                            <input type="number" readonly value=""
                                                   class="update_rate_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->program.'_'.$value->station.'_'.$duration)) }}"
                                                   id="ur{{ $duration }}{{$value->id}}" data_12="{{ $value->id}}"
                                                   data_11="60" data_10="" data_9="">
                                        </td>
                                    @endif

                                    <td>
                                        <a href="#discount_modal_{{ $duration.'_'.$value->id }}" class="modal_click">
                                            <input type="number" class="referesh_discount_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->station)) }}" readonly value="{{ $value->volume_discount }}" id="vd{{ $duration }}{{$value->id}}" data_12="{{ $value->id}}" data_11="60" data_10="" data_9="">
                                        </a>
                                    </td>

                                    <td>
                                        <input type="number" readonly id="exposure_{{ $duration.'_'.$value->id }}" name="exposure">
                                    </td>

                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++)
                                        @if($fayaFound['days'][$i]==$value->day )
                                            <td>
                                                <input type="number" id="{{ $duration }}{{$value->id}}" class="day_input input_value_class{{ $duration.'_'.$value->id }} get_value{{ $fayaFound['dates'][$i].$value->id.$duration }}" data_12="{{ $value->id}}"
                                                        data_11="15" data_10="{{$fayaFound['dates'][$i]}}" data-update_exposure="{{ $duration.'_'.$value->id }}" data-get_data_value="{{$fayaFound['dates'][$i].$value->id.$duration}}"
                                                        data_9="{{$fayaFound['days'][$i]}}">
                                            </td>
                                        @else
                                            <td id="">
                                                <input type="number" id="{{ $duration }}{{$value->id}}" class="disabled_input" data_12="{{ $value->id}}"
                                                       data_11="15" data_10=""
                                                       data_9="" disabled>
                                            </td>
                                        @endif
                                    @endfor
                                </tr>

                                {{--modal for discount--}}
                                <div class="modal_contain" style="width: 1000px;" id="discount_modal_{{ $duration.'_'.$value->id }}">
                                    <div class="the_frame clearfix mb border_top_color pt load_this_div">
                                        <form action="{{ route('media_plan.volume_discount.store') }}" data-get_station_discount="{{ $value->station }}" data-get_volume_id="{{ $duration.'_'.$value->id }}" class="submit_discount_form" method="post" id="submit_discount_{{ $duration.'_'.$value->id }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="margin_center col_11 clearfix pt4 create_fields">

                                                <div class="clearfix mb3">
                                                    <div class="input_wrap column col_8 {{ $errors->has('discount') ? ' has-error' : '' }}">
                                                        <label class="small_faint">Discount</label>
                                                        <div class="">
                                                            <input type="text" name="discount" value="{{ $value->volume_discount }}" required placeholder="Volume Discount">

                                                            @if($errors->has('discount'))
                                                                <strong>
                                                                    <span class="help-block">
                                                                        {{ $errors->first('discount') }}
                                                                    </span>
                                                                </strong>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="station" value="{{ $value->station }}">
                                                <div class="mb4 align_right pt">
                                                    <input type="submit" value="Create Discount" id="submit_15{{ $value->id }}" class="btn uppercased mb4">
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>

                            @endforeach

                            </tbody>
                        </table>
                        <!-- end -->
                    </div>
                </div>

            </div>
        @endforeach
        @include('agency.mediaPlan.includes.program-modal')
    </div>

    <div class="clearfix mb">
      <div class="input_wrap column col_4">
        <div class="select_wrap{{ $errors->has('client') ? ' has-error' : '' }}">
          <label class="small_faint">Select Client</label>
          <select name="client" id="client_name" required>
              @foreach($clients as $client)
              <option value="{{ $client->id }}" @if((Session::get('campaign_information')) !=null)
                  @if($campaign_general_information->client === $client->id))
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


      <div class="input_wrap column col_4">
        <label class="small_faint">Product name</label>
        <input type="text" id="product_name" name="age_groups[0][max]" placeholder="Product name">
        <input type="hidden" id="plan_id" value="{{$fayaFound['programs_stations'][0]->media_plan_id}}">
      </div>
    </div>

    <div class="action_footer client_dets mb4 mt4">
      <div class="col_6 column">
        <button type="button" id="back_btn" class="btn small_btn show" onclick="goBack()">Back</button>
      </div>
      <div class="col_6 column">
        <button type="button" id="save_progress" class="btn small_btn right show summary">Summary</button>
        <button type="button" id="mpo_filters" class="btn small_btn right show save mr-2">Save</button>
      </div>
    </div>

    <!-- <div class="action_footer client_dets mb4 mt4">
      <div class="col_6 column">
        <button type="button" id="back_btn" class="btn small_btn show" onclick="goBack()">Back</button>
      </div>
      <div class="col_6 column">
        <button type="button" id="mpo_filters" class="btn small_btn right show">Create Plan</button>
        <button type="button" id="save_progress" class="btn small_btn right save mr-2">Save</button>
      </div>
    </div> -->

    <br><br><br><br><br><br><br>

</div>

{{-- modal --}}
<div id="ex1" class="modal">
  <p>Thanks for clicking. That felt good.</p>
  <a href="#" rel="modal:close">Close</a>
</div>
@stop

@section('scripts')
<script src="https://unpkg.com/flatpickr"></script>
<script type="text/javascript" src="{{ asset('new_frontend/js/wickedpicker.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
  function goBack() {
    window.history.back();
  }
</script>
<script>
/*    function replaceDocument(id) {
        id = '5cac9ebd77e1b';
        window.history.pushState({}, '', `/agency/media-plan/createplan/${id}`);
    } */
    <?php echo "var days =".json_encode($days).";\n"; ?>
    <?php echo "var media_plans =".json_encode($fayaFound['programs_stations']).";\n"; ?>
    <?php echo "var media_plan_programs =".json_encode($media_plans_programs).";\n"; ?>
    <?php echo "var durations =".json_encode($default_material_length).";\n"; ?>
    $.each(durations, function (index, value) {
        $('#'+value+'SecRevealer').click(function() {
            $('#'+value+'SecBox').toggle('medium');
        });
    });

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#simplemodal-overlay').hide();
        $.each(durations, function (index, value) {
            $('#'+value+'SecBox').hide();

            $("#default_mpo_table_"+value).clone(true).appendTo('#table-scroll_'+value).addClass('clone');
        });

        var plans = [];
        var url = window.location.href;
        var trim = url.split('/');
        var fifthSegment = trim[6];
        //var summed_value = [];
        $.each(durations, function (duration_index, duration_value) {
            $.each(media_plans, function (plan_index, plan_value) {
                var summed_value = [];
                $('.input_value_class'+duration_value+'_'+plan_value.id).keyup(function (e) {
                    var sum = 0;
                    var target_class_value = $(this).data('get_data_value');
                    var value_of_input = $('.get_value'+target_class_value).val();
                    var date = $(this).attr("data_10");
                    var exposure_id_value = $(this).data('update_exposure');
                    var number_value = parseInt(value_of_input);
                    if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                        return false;
                    }else{
                        if(!isNaN(number_value)){
                            for(var i = 0; i < summed_value.length; i++){
                                if ( summed_value[i].date === date) {
                                    summed_value.splice(i, 1);
                                }
                            }
                            summed_value.push({
                                'date' : date,
                                'value' : number_value,
                            });
                        }else{
                            for(var i = 0; i < summed_value.length; i++){
                                if ( summed_value[i].date === date) {
                                    summed_value.splice(i, 1);
                                }
                            }
                        }
                    }
                    for(var i = 0; i < summed_value.length; i++){
                        sum += summed_value[i].value;
                    }
                    $("#exposure_"+exposure_id_value).val(sum);
                });
            })
        })

        $(".day_input").change(function () {
            var value_button = $(this).attr("data_12");
            var duration = $(this).attr("data_11");
            var date = $(this).attr("data_10");
            var day = $(this).attr("data_9");
            var slot = $("#" + duration + value_button).val();
            var unit_rate = $("#ur" + duration + value_button).val();
            var volume_disc = $("#vd" + duration + value_button).val();
            if (plans.length > 0) {
              for (var i = 0; i < plans.length; i++) {
                if (plans[i].id == value_button && plans[i].date == date && plans[i].duration == duration) {
                  plans.splice(i, 1)
                }
              }
            }
            plans.push({
                'id': value_button,
                'material_length': duration,
                "unit_rate": unit_rate,
                "volume_disc": volume_disc,
                'date': date,
                'day': day,
                'slot': slot
            });
        });

        var i = $("select .b").length;
        var max = 12;
        $.each(days, function (index, value) {
            $('body').delegate("#add_more_"+value, 'click', function () {
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

        //submitting the form
        $(".submit_form").on('submit', function(e) {
            $('.load_this_div').css({
                opacity : 0.2
            });
            event.preventDefault(e);
            var form_id = $(this).data('get_id');
            var formdata = $("#submit_"+form_id).serialize();
            $.ajax({
                cache: false,
                type: "POST",
                url : '/agency/media-plan/store-programs',
                dataType: 'json',
                data: formdata,
                success: function (data) {
                    if(data.programs){
                        console.log(data.ratings, data.programs);
                        $.each(durations, function (index, duration_value) {
                            $.each(data.programs, function (data_index, data_value) {
                                var un_val = convertToSlug(data_value.day+'_'+data_value.station+'_'+data_value.start_time);
                                var rating_class = convertToSlug(data_value.program_name+'_'+data_value.station+'_'+duration_value);
                                $('.update_program_a_'+un_val).text(data_value.program_name);
                                $('.update_program_modal_'+un_val).val(data_value.program_name);
                                if($(this).data('get_duration') === duration_value){
                                    $('.update_rating_class_'+un_val).addClass(rating_class);
                                }
                            });
                            $.each(data.ratings, function (rating_index, rating_value) {
                                if(duration_value === rating_value.duration){
                                    var ratings_unique = convertToSlug(rating_value.program_name + '_' + rating_value.station + '_' + duration_value);
                                    console.log(ratings_unique);
                                    $('input.update_rate_' + ratings_unique).val(rating_value.price)
                                }
                            });
                        });

                        toastr.success('Program Updated successful');
                        $.modal.close();
                        $('.load_this_div').css({
                            opacity : 1
                        });
                    }else{
                        toastr.error('Cannot Create program');
                        $('.load_this_div').css({
                            opacity : 1
                        });
                    }
                },
                error : function (xhr) {
                    if(xhr.status === 500){
                        toastr.error('An unknown error has occurred, please try again');
                        $('.load_this_div').css({
                            opacity: 1
                        });
                        return;
                    }else if(xhr.status === 503){
                        toastr.error('The request took longer than expected, please try again');
                        $('.load_this_div').css({
                            opacity: 1
                        });
                        return;
                    }else{
                        toastr.error('An unknown error has occurred, please try again');
                        $('.load_this_div').css({
                            opacity: 1
                        });
                        return;
                    }
                }
            })
        });

        $(".submit_discount_form").on('submit', function(e) {
            $('.load_this_div').css({
                opacity : 0.2
            });
            event.preventDefault(e);
            var form_id = $(this).data('get_volume_id');
            var station = convertToSlug($(this).data('get_station_discount'));
            var formdata = $("#submit_discount_"+form_id).serialize();
            $.ajax({
                cache: false,
                type: "POST",
                url : '/agency/media-plan/store-volume-discount',
                dataType: 'json',
                data: formdata,
                success: function (data) {
                    if(data.data){
                        $(".referesh_discount_"+station).val(data.data.discount);
                        toastr.success('Volume Discount Added successful');
                        $.modal.close();
                        $('.load_this_div').css({
                            opacity : 1
                        });
                        //location.reload();
                    }else{
                        toastr.error('Cannot Add Volume Discount');
                        $('.load_this_div').css({
                            opacity : 1
                        });
                    }
                },
                error : function (xhr) {
                    if(xhr.status === 500){
                        toastr.error('An unknown error has occurred, please try again');
                        $('.load_this_div').css({
                            opacity: 1
                        });
                        return;
                    }else if(xhr.status === 503){
                        toastr.error('The request took longer than expected, please try again');
                        $('.load_this_div').css({
                            opacity: 1
                        });
                        return;
                    }else{
                        toastr.error('An unknown error has occurred, please try again nnn');
                        $('.load_this_div').css({
                            opacity: 1
                        });
                        return;
                    }
                },
            })
        });

        function convertToSlug(Text)
        {
            return Text
                .toLowerCase()
                .replace(/[^a-zA-Z0-9]+/g,'')
                .replace(/ +/g,'-')
                .replace('/:/g', '')
                ;
        }


        $("body").delegate(".remove_already", "click", function () {
            event.preventDefault();
            var button_id = $(this).data("button_id");
            $(".remove_div_already_had_"+button_id).remove();

        });

        function save_plans(forward) {
            if (plans.length == 0) {
                // swal("input atleast one spot");
                toastr.error("Please choose at least one spot.");
                $('.show').prop('disabled', false);
                return;
            }

            var client_name = $("#client_name").val();
            var product_name = $("#product_name").val();
            if (client_name == "Select Client" && product_name == "") {
                // swal("Select client and enter product name");
                toastr.error("Please select a client and brand and product");
                $('.show').prop('disabled', false);
                return;
            }

            $('.save').prop('disabled', true);
            var plan_id = $("#plan_id").val();
            var body = {
                "_token": "{{ csrf_token() }}",
                "client_name": client_name,
                "product_name": product_name,
                "plan_id": plan_id,
                "data": JSON.stringify(plans)
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "/agency/media-plan/finish_plan",
                data: body,
                beforeSend: function(data) {
                    // run toast showing progress
                    toastr_options = {
                        "preventDuplicates": true,
                        "tapToDismiss": false,
                        "hideDuration": "1",
                        "timeOut": "300000000"
                    };
                    msg = "Saving, please wait";
                    toastr.info(msg, null, toastr_options)
                },
                success: function(data){
                    toastr.clear();
                    if(data.status === 'success'){
                        if (forward) {
                            toastr.success("Plans successfully saved! Going to summary")
                        } else {
                            toastr.success("Plans successfully saved!")
                        }
                        $('.save').prop('disabled', false);
                        $("#load_this").css({opacity : 1});
                        if (forward) {
                            location.href = '/agency/media-plan/summary/' + plan_id;
                        }
                    }else{
                        toastr.error("Error saving plans.");
                        $("#load_this").css({opacity : 1});
                        $('.save').prop('disabled', false);
                    }
                },
                error: function(xhr){
                    toastr.clear();
                    toastr.error('An unknown error has occurred, please try again');
                    $('.load_this').css({opacity: 1});
                    $('.save').prop('disabled', false);
                    return;
                }
            });
        };

        $("body").delegate(".summary", "click", function () {
            save_plans(true);
        });

        $("body").delegate(".save", "click", function () {
            save_plans(false);
            // $('.save').prop('disabled', true);
            // var client_name = $("#client_name").val();
            // var product_name = $("#product_name").val();
            // var plan_id = $("#plan_id").val();
            // var body = {
            //     "_token": "{{ csrf_token() }}",
            //     "client_name": client_name,
            //     "product_name": product_name,
            //     "plan_id": plan_id,
            //     "data": JSON.stringify(plans)
            // }
            // if (plans.length == 0) {
            //     swal("input atleast one spot");
            //     $('.show').prop('disabled', false);
            // } else {
            //     if (client_name == "Select Client" && product_name == "") {
            //         swal("Select client and enter product name");
            //         $('.show').prop('disabled', false);
            //     } else {
            //         $.ajax({
            //             type: "POST",
            //             dataType: 'json',
            //             url: "/agency/media-plan/finish_plan",
            //             data: body,
            //             success: function (data) {
            //                 swal("Success!", "Plans successfully selected!", "success")
            //                     .then((value) => {
            //                         location.href = '/agency/media-plan/summary/' +
            //                             fifthSegment;
            //                     });
            //             },

            //             error: function () {
            //                 alert("some error");
            //                 $('.show').prop('disabled', false);
            //             }
            //         });
            //     }
            // }

        });
    });
</script>
@stop

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('new_frontend/css/wickedpicker.min.css') }}">
<style>
    .t_picker {
        z-index: 100000 !important;
        position: inherit;
    }
</style>
@stop
