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
    <div id="refresh_this_stuff">
        @foreach($default_material_length as $duration)
            <div class="the_frame client_dets mb4 load_this_div">

                <div class="filters border_bottom clearfix">
                    <div class="column col_8 p-t">
                        <p class="uppercased weight_medium revealer" id="{{ $duration }}SecRevealer">{{ $duration }} sec </p>
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
                                        @if($value->program == 'Unknown Program')
                                            <a href="#program_modal_{{ $duration }}{{ $value->id }}" class="modal_click">{{ $value->program }}</a>
                                        @else
                                            <a href="#edit_program_modal_{{ $duration }}{{ $value->id }}" class="modal_click">
                                                {{ $value->program }}
                                            </a>
                                        @endif
                                    </td>
                                    <td id="btn" class="fixed-side new_program_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->day.'_'.$value->station.'_'.$value->start_time)) }}" style="display: none;">
                                        <a href="#program_modal_{{ $duration }}{{ $value->id }}" class="modal_click"></a>
                                    </td>

                                    <td id="btn" class="fixed-side" style="display: none;">
                                        <a href="#program_modal_{{ $duration }}{{ $value->id }}" class="modal_click"></a>
                                    </td>
                                    @foreach(json_decode($value->duration_lists) as $key => $duration_list)
                                        @if($duration_list == $duration)
                                            <td>
                                                <input type="number" readonly value="{{ json_decode($value->rate_lists)[$key] }}"
                                                       class="update_rate_{{ $duration_list.'_'.json_decode($value->rate_lists)[$key] }}"
                                                       id="ur{{ $duration }}{{$value->id}}" data_12="{{ $value->id}}"
                                                       data_11="60" data_10="" data_9="">
                                            </td>
                                        @endif
                                    @endforeach

                                    <td>
                                        <input type="number" value="0" id="vd{{ $duration }}{{$value->id}}" data_12="{{ $value->id}}" data_11="60" data_10="" data_9="">
                                    </td>

                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++) @if($fayaFound['days'][$i]==$value->day )
                                        <td>
                                            <input type="number" id="{{ $duration }}{{$value->id}}" class="day_input" data_12="{{ $value->id}}"
                                                    data_11="15" data_10="{{$fayaFound['dates'][$i]}}"
                                                    data_9="{{$fayaFound['days'][$i]}}">
                                        </td>
                                    @else
                                        <td id="">
                                            <input class="disabled_input" type="number" placeholder="" name="lname" disabled>
                                        </td>
                                    @endif
                                    @endfor
                                </tr>

                            @endforeach

                            </tbody>
                        </table>
                        <!-- end -->
                    </div>
                </div>

            </div>
        @endforeach
        @include('agency.mediaPlan.includes.program-modal')
        @include('agency.mediaPlan.includes.update_program')
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
        <button type="button" id="mpo_filters" class="btn small_btn right show">Create Plan</button>
      </div>
    </div>

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
                console.log(big_html);
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
                        /*$.each(data.programs, function (data_index, data_value) {
                            var un_val = convertToSlug(data_value.day+'_'+data_value.station+'_'+data_value.start_time);
                            $('td.update_program_'+un_val).remove();
                            $('td.new_program_'+un_val).append(data_value.program_name);
                            $('td.new_program_'+un_val).show();
                        });
                        $.each(data.ratings, function (rating_index, rating_value) {
                            $('.update_rate_'+rating_value.duration+'_'+rating_value.price).val(rating_value.price)
                        });*/
                        $("#refresh_this_stuff").load(location.href+" #refresh_this_stuff>*","");
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

        $(".update_form").on('submit', function(e) {
            $('.load_this_div').css({
                opacity : 0.2
            });
            event.preventDefault(e);
            var form_id = $(this).data('get_id_update');
            var formdata = $("#update_"+form_id).serialize();
            $.ajax({
                cache: false,
                type: "POST",
                url : '/agency/media-plan/store-programs',
                dataType: 'json',
                data: formdata,
                success: function (data) {
                    if(data.programs){
                        /*$.each(data.programs, function (data_index, data_value) {
                            var un_val = convertToSlug(data_value.day+'_'+data_value.station+'_'+data_value.start_time);
                            $('td.update_program_'+un_val).remove();
                            $('td.new_program_'+un_val).append(data_value.program_name);
                            $('td.new_program_'+un_val).show();
                        });
                        console.log(data.ratings, data.programs);
                        $.each(data.ratings, function (rating_index, rating_value) {
                            $('input.update_rate_'+rating_value.duration+'_'+rating_value.price).val(rating_value.price)
                        });*/
                        $("#refresh_this_stuff").load(location.href+" #refresh_this_stuff>*","");
                        toastr.success('Program Updated successful');
                        $.modal.close();
                        $('.load_this_div').css({
                            opacity : 1
                        });
                        location.reload();
                    }else{
                        toastr.error('Cannot Update program');
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
                },
            })
        });

        $("body").delegate(".remove_already", "click", function () {
            event.preventDefault();
            var button_id = $(this).data("button_id");
            $(".remove_div_already_had_"+button_id).remove();

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


        $("body").delegate(".show", "click", function () {
            $('.show').prop('disabled', true);
            var client_name = $("#client_name").val();
            var product_name = $("#product_name").val();
            var product_name = $("#product_name").val();
            var plan_id = $("#plan_id").val();
            var body = {
                "_token": "{{ csrf_token() }}",
                "client_name": client_name,
                "product_name": product_name,
                "plan_id": plan_id,
                "data": JSON.stringify(plans)
            }
            if (plans.length == 0) {
                swal("input atleast one spot");
                $('.show').prop('disabled', false);
            } else {

                if (client_name == "Select Client" && product_name == "") {
                    swal("Select client and enter product name");
                    $('.show').prop('disabled', false);
                } else {

                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        url: "/agency/media-plan/finish_plan",
                        data: body,
                        success: function (data) {
                            swal("Success!", "Plans successfully selected!", "success")
                                .then((value) => {
                                    location.href = '/agency/media-plan/summary/' +
                                        fifthSegment;
                                });
                        },

                        error: function () {
                            alert("some error");
                            $('.show').prop('disabled', false);
                        }
                    });
                }
            }

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
