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
                        <p class="uppercased weight_medium revealer" id="{{ $duration }}SecRevealer">{{ $duration }} sec </p> <p class="update_{{ $duration }}"></p>
                    </div>
                </div>

                <!-- campaigns table -->
                <div id="table-scroll" class="table-scroll_{{ $duration }} refresh_data_{{ $duration }}">
                    <div class="accordion-group table-wrap" id="{{ $duration }}SecBox">
                        <table class="display exposure_data default_mpo filter_mpo" id="default_mpo_table_{{ $duration }}">
                            <thead>
                            <tr>
                                <th class="fixed-side">Station</th>
                                <th class="fixed-side">Day</th>
                                <th class="fixed-side">Time Belt</th>
                                <th class="fixed-side" id="last-fixed">Program</th>
                                <th class="fixed-side">Unit Rate</th>
                                <th class="fixed-side">Volume Disc</th>
                                <th class="fixed-side">Total Exposure</th>
                                <th class="fixed-side">Net Total</th>
                                @for ($i = 0; $i < count($fayaFound['labeldates']); $i++)
                                    <th>{{ $fayaFound['labeldates'][$i] }}</th>
                                @endfor

                            </tr>
                            </thead>
                            <tbody>

                            @foreach($fayaFound['programs_stations'] as $value)
                                <tr class="{{ $value->program}} exposure_table">
                                    <td id="btn" class="fixed-side">{{ $value->station }}</td>
                                    <td id="btn" class="fixed-side">{{ substr($value->day, 0, 3) }}</td>
                                    <td id="btn" class="fixed-side"> {{ substr($value->start_time,0,5) }} {{ substr($value->end_time,0,5) }}</td>
                                    <td id="btn" class="fixed-side update_program_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->day.'_'.$value->station.'_'.$value->start_time)) }}">
                                        <a href="#program_modal_{{ $duration }}{{ $value->id }}" class="modal_click update_program_a_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->day.'_'.$value->station.'_'.$value->start_time)) }}">{{ $value->program }}</a>
                                    </td>
                                    <input type="hidden" class="id" value="{{ $value->id }}">
                                    <input type="hidden" class="material_duration" value="{{ $duration }}">
                                    @if($value->duration_lists != "[null]")
                                        <td class="fixed-side">
                                            <input type="number" readonly
                                                   @foreach(json_decode($value->duration_lists) as $key => $duration_list)
                                                       @if($duration_list == $duration)
                                                            value="{{ json_decode($value->rate_lists)[$key] }}"
                                                       @endif
                                                   @endforeach
                                                           name="unit_rate"
                                                   class="update_rating_class_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->day.'_'.$value->station.'_'.$value->start_time)) }}
                                                       update_rate_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->program.'_'.$value->station.'_'.$duration)) }}
                                                       unit_rate_val_{{ $duration.'_'.$value->id }}
                                                       unit_rate"
                                                   id="ur_{{ $duration.'_'.$value->id}}" data_12="{{ $value->id}}"
                                                   data_11="60" data_10="" data_9=""
                                                   style="width: 70px;"
                                            >
                                        </td>
                                    @else
                                        <td class="fixed-side">
                                            <input type="number" readonly value=""
                                                   class="update_rate_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->program.'_'.$value->station.'_'.$duration)) }}
                                                       unit_rate_val_{{ $duration.'_'.$value->id }}
                                                       unit_rate"
                                                   name="unit_rate"
                                                   id="ur_{{ $duration.'_'.$value->id}}" data_12="{{ $value->id}}"
                                                   data_11="60" data_10="" data_9=""
                                                   style="width: 70px;"
                                            >
                                        </td>
                                    @endif

                                    <td class="fixed-side">
                                        <a href="#discount_modal_{{ $duration.'_'.$value->id }}" class="modal_click">
                                            <input type="number"
                                                   name="volume_discount"
                                                   class="volume_discount referesh_discount_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->station)) }} discount_val_{{ $duration.'_'.$value->id }}"
                                                   readonly value="{{ $value->volume_discount }}"
                                                   id="vd_{{ $duration.'_'.$value->id}}"
                                                   data_12="{{ $value->id}}"
                                                   data_11="60" data_10="" data_9=""
                                                style="width: 50px;"
                                            >
                                        </a>
                                    </td>

                                    <td class="fixed-side">
                                        <input type="number" readonly id="exposure_{{ $duration.'_'.$value->id }}"
                                               @if($value->material_length != "")
                                                   @foreach(json_decode($value->material_length) as $media_length_data)
                                                        @if($media_length_data[0]->material_length == $duration)
                                                            @foreach($media_length_data as $media)
                                                                value="{{ collect($media_length_data)->sum('slot') }}"
                                                            @endforeach
                                                        @endif
                                                   @endforeach
                                               @endif
                                               name="exposure"
                                               style="width: 50px;"
                                               class="total_exposure"
                                        >
                                    </td>

                                    <td class="fixed-side">
                                        <input type="number"
                                               readonly id="net_total_{{ $duration.'_'.$value->id }}"
                                               class="get_duration_net_{{ $duration }} net_total"
                                               @if($value->material_length != "")
                                                   @foreach(json_decode($value->material_length) as $media_length_data)
                                                       @if($media_length_data[0]->material_length == $duration)
                                                           @foreach($media_length_data as $media)
                                                                value="{{ array_last($media_length_data)->net_total }}"
                                                           @endforeach
                                                       @endif
                                                   @endforeach
                                               @endif
                                               name="net_total"
                                               style="width: 100px;"
                                        >
                                    </td>

                                    @for ($i = 0; $i < count($fayaFound['dates']); $i++)
                                        @if($fayaFound['days'][$i]==$value->day )
                                            <td>
                                                <input type="hidden" class="date_duration" value="{{ $duration }}">
                                                <input type="hidden" class="dates" value="{{ $fayaFound['dates'][$i] }}">
                                                <input type="hidden" class="days" value="{{ $fayaFound['days'][$i] }}">
                                                <input type="number"
                                                       id="unit_exposure_{{ $duration.'_'.$value->id }}"
                                                       class="day_input unit_exposures input_value_class{{ $duration.'_'.$value->id }}
                                                       unit_exposure
                                                       get_value{{ $fayaFound['dates'][$i].$value->id.$duration }}"
                                                       data_12="{{ $value->id}}"
                                                       data_11="{{ $duration }}"
                                                       data_10="{{$fayaFound['dates'][$i]}}"
                                                       data-update_exposure="{{ $duration.'_'.$value->id }}"
                                                       data-get_data_value="{{$fayaFound['dates'][$i].$value->id.$duration}}"
                                                       data-duration="{{ $duration }}"
                                                       data-required_values="{{ $fayaFound['dates'][$i].$value->id.$duration }}"
                                                       data-id="{{ $value->id }}"
                                                       data-get_select_factor="{{ $duration.'_'.$value->id }}"
                                                       data_9="{{$fayaFound['days'][$i]}}"
                                                       data-dates="{{ $fayaFound['dates'][$i] }}"
                                                       data-days="{{ $fayaFound['days'][$i] }}"
                                                       @if($value->material_length != "")
                                                           @foreach(json_decode($value->material_length) as $media_length_data)
                                                               @if($media_length_data[0]->material_length == $duration)
                                                                   @foreach($media_length_data as $media)
                                                                       @if($media->date == $fayaFound['dates'][$i] && $media->id == $value->id)
                                                                           value="{{ $media->slot }}"
                                                                       @endif
                                                                   @endforeach
                                                               @endif
                                                           @endforeach
                                                       @endif
                                                       style="width: 60px;"
                                                >
                                            </td>
                                        @else
                                            <td id="">
                                                <input type="number" id="{{ $duration }}{{$value->id}}" class="disabled_input" data_12="{{ $value->id}}"
                                                       data_11="15" data_10=""
                                                       data_9="" style="width: 60px;" disabled>
                                            </td>
                                        @endif
                                    @endfor
                                </tr>

                                {{--modal for discount--}}
                                <div class="modal_contain" style="width: 100%; max-width: 30%; padding: 0;" id="discount_modal_{{ $duration.'_'.$value->id }}">
                                    <div class="the_frame clearfix border_top_color pt load_this_div">
                                        <form action="{{ route('media_plan.volume_discount.store') }}" data-get_station_discount="{{ $value->station }}" data-get_volume_id="{{ $duration.'_'.$value->id }}" class="submit_discount_form" method="post" id="submit_discount_{{ $duration.'_'.$value->id }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="margin_center col_11 clearfix pt4 create_fields">

                                                <div class="clearfix">
                                                    <div class="input_wrap column col_12 {{ $errors->has('discount') ? ' has-error' : '' }}">
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
                                                <div class=" align_right pt">
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
        @include('agency.mediaPlan.includes.discount_modal')
    </div>

    <div class="clearfix mb load_stuff">
        <div class="input_wrap column col_6">
            <label class="small_faint">Clients</label>

            <div class="select_wrap{{ $errors->has('client') ? ' has-error' : '' }}">
                <select name="client" id="clients" required>
                    <option>Select Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}"
                            @if($media_plan->client_id === $client->id)
                                selected
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
                @if($media_plan->brand_id != "")
                    <select name="brand" id="brand">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                                @if($media_plan->brand_id === $brand->id)
                                    selected
                                @endif
                            >{{ $brand->name }}</option>
                        @endforeach
                    </select>
                @else
                    <select name="brand" id="brand"></select>
                @endif
                @if($errors->has('brand'))
                    <strong>{{ $errors->first('brand') }}</strong>
                @endif
            </div>
        </div>
    </div>
    <div class="clearfix mb">
        <div class="col_6 input_wrap{{ $errors->has('product') ? ' has-error' : '' }}">
            <label class="small_faint">Product</label>
            <input type="text" required name="product" @if($media_plan->product_name) value="{{ $media_plan->product_name }}" @endif id="product" placeholder="Product">

            @if($errors->has('product'))
                <strong>
                    <span class="help-block">{{ $errors->first('product') }}</span>
                </strong>
            @endif
        </div>
    </div>
    <input type="hidden" name="plan_id" id="plan_id" value="{{ $plan_id }}">

    <div class="action_footer client_dets mb4 mt4">
      <div class="col_6 column">
        <a id="back_btn" href="{{ route('agency.media_plan.customize', ['id'=>$plan_id]) }}" class="btn small_btn"><i class="media-plan material-icons">navigate_before</i> Back</a>
      </div>
      <div class="col_6 column">
        @if($media_plan->status == 'Approved' || $media_plan->status == 'Declined')
        <a href="{{ route('agency.media_plan.summary', ['id'=>$plan_id]) }}" class="media-plan btn small_btn right mr-2 next-page-btn">Next <i class="media-plan material-icons">navigate_next</i></a>
        @else
        <button type="button" id="save_progress" class="btn small_btn right show summary">Summary</button>
        @endif
        <button type="button" id="mpo_filters" class="media-plan btn small_btn right show save mr-2 {{ ($media_plan->status == 'Approved' || $media_plan->status == 'Declined') ? 'disabled-action-btn':''}}"><i class="media-plan material-icons">save</i>Save</button>
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
    <?php echo "var days =".json_encode($days).";\n"; ?>
    <?php echo "var media_plans =".json_encode($fayaFound['programs_stations']).";\n"; ?>
    <?php echo "var durations =".json_encode($default_material_length).";\n"; ?>
    $.each(durations, function (index, value) {
        $('#'+value+'SecRevealer').click(function() {
            $('#'+value+'SecBox').toggle('medium');
        });
    });

    $(document).ready(function () {

        $('#simplemodal-overlay').hide();

        $.each(durations, function (index, value) {
            $('#'+value+'SecBox').hide();

            $("#default_mpo_table_"+value).clone(true).appendTo('#table-scroll_'+value).addClass('clone');
        });
        var slot_object = [];

        $.each(durations, function (duration_index, duration_value) {
            var sum_net = [];
            $.each(media_plans, function (plan_index, plan_value) {
                $('.input_value_class'+duration_value+'_'+plan_value.id).keyup(function (e) {
                    var sum = 0;
                    $('.input_value_class'+duration_value+'_'+plan_value.id).each(function() {
                        if($(this).val() !== ""){
                            sum += parseInt($(this).val())
                        }
                    });
                    //console.log(sum)
                    var net_sumation = 0;
                    var exposure_id_value = $(this).data('update_exposure');
                    var id = $(this).data('id');
                    var duration = $(this).data('duration');
                    $("#exposure_"+exposure_id_value).val(sum);
                    var discount = $('.discount_val_'+exposure_id_value).val();
                    var unit_rate = $('.unit_rate_val_'+exposure_id_value).val();
                    var gross_total = unit_rate * sum;
                    var deducted_value = (discount/100) * gross_total;
                    var net_total = gross_total - deducted_value;
                    $('#net_total_'+exposure_id_value).val(net_total);
                    for(var i = 0; i < sum_net.length; i++){
                        if ( sum_net[i].id === id) {
                            sum_net.splice(i, 1);
                        }
                    }
                    sum_net.push({
                        'id' : id,
                        'value' : net_total,
                    });
                    for(var i = 0; i < sum_net.length; i++){
                        net_sumation += sum_net[i].value;
                    }
                    $('.update_'+duration).text('Sub Total : ₦'+(net_sumation + 0.001).toLocaleString().slice(0,-1));
                });
            })
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
            $("table.exposure_data > tbody > tr").each(function() {
                var rating_data = populate_rating_data($(this));
                $(this).find("input.unit_exposures").each(function () {
                    populate_exposures(rating_data, $(this));
                });
            });
            if (slot_object.length === 0) {
                toastr.error("Please choose at least one spot.");
                $('.show').prop('disabled', false);
                return;
            }
            var client_name = $("#clients").val();
            var product_name = $("#product").val();
            var brand_id = $("#brand").val();
            if (client_name === "Select Client" || product_name === "" || brand_id === "") {
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
                "brand_id": brand_id,
                "plan_id": plan_id,
                "data": JSON.stringify(slot_object)
            };
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

        function populate_exposures(rating_data, this_object)
        {
            if(this_object.val() !== ''){
                var exposure_date = this_object.data("dates");
                var  exposure_slot = this_object.val();
                var  exposure_days = this_object.data("days");
                slot_object.push({
                    'id' : rating_data.suggestion_id,
                    'material_length' : rating_data.material_duration,
                    'unit_rate' : rating_data.unit_rate,
                    'volume_disc' : rating_data.volume_discount,
                    'date': exposure_date,
                    'day' : exposure_days,
                    'slot' : exposure_slot,
                    'exposure' : rating_data.exposure,
                    'net_total' : rating_data.net_total
                })
            }
        }

        function populate_rating_data(this_object)
        {
            var unit_rate = this_object.find("input.unit_rate").val();
            var volume_discount = this_object.find("input.volume_discount").val();
            var exposure = this_object.find("input.total_exposure").val();
            var net_total = this_object.find("input.net_total").val();
            var suggestion_id = this_object.find("input.id").val();
            var material_duration = this_object.find("input.material_duration").val();
            return {
                'unit_rate' : unit_rate,
                'volume_discount' : volume_discount,
                'exposure' : exposure,
                'net_total' : net_total,
                'suggestion_id' : suggestion_id,
                'material_duration' : material_duration
            }
        }

        $("body").delegate(".summary", "click", function () {
            save_plans(true);
        });

        $("body").delegate(".save", "click", function () {
            save_plans(false);
        });

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
                            var big_html = '<select name="brand" id="brand">';
                            if(data.brands !== ''){
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
                                $('a').css('pointer-events','');
                                $('.button_create').show();
                            }
                        }else{
                            $(".load_stuff").css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            toastr.error('An error occurred, please contact the administrator')
                        }

                    },
                    error : function (xhr) {
                        toastr.clear();
                        if (xhr.status === 500) {
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_stuff').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events', '');
                            $('.button_create').show();
                            return;
                        } else if (xhr.status === 503) {
                            toastr.error('The request took longer than expected, please try again');
                            $('.load_stuff').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events', '');
                            $('.button_create').show();
                            return;
                        } else {
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_stuff').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events', '');
                            $('.button_create').show();
                            return;
                        }
                    }
                });
            }else{
                $("#industry").val('');
                $("#sub_industry").val('');
            }
        });

        var countTimeBelt = 0;

        $("body").delegate(".addTimeBelt", "click", function () {
            var day = $(this).data('day');
            var html = '<div class="column col-12 left no_left_margin timeBelt_'+countTimeBelt+'">\
                          <div class="input_wrap input_wrap_inlin_lbl column col_5">\
                              <label class="small_faint">Start Time</label>\
                              <input type="text" id="timepicker" name="start_time[]" class="t_picker timepicker_'+day+'"/>\
                          </div>\
                          <div class="input_wrap input_wrap_inlin_lbl column col_5">\
                              <label class="small_faint">End Time</label>\
                              <input type="text" id="timepicker" name="end_time[]" class="t_picker timepicker_'+day+'"/>\
                          </div>\
                          <div class="column col_2 left no_left_margin">\
                              <button type="button" data-count="'+countTimeBelt+'" class="btn small_btn mbl bg_red uppercased deleteTimeBelt">\
                                  <i class="material-icons">delete_forever</i>\
                              </button>\
                          </div>\
                        </div>';

            $('.weekly_day_'+day).append(html);
            countTimeBelt++;
        });

        $("body").delegate(".deleteTimeBelt", "click", function () {
            var count = $(this).data('count');
            $('.timeBelt_'+count).remove();
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
