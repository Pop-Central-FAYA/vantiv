@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Create Campaign Step 5 </title>
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
        <div class="the_frame clearfix mb border_top_color">

            <div class="margin_center col_10 clearfix pt4 create_fields">

                <!-- progress bar -->
                <div class="create_gauge clearfix">
                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>
                </div>

                <!-- media houses -->
                <div class="media_houses mb3 clearfix" style="margin-top: -30px;">
                    @foreach($ads_broads as $ads_broad)
                        <div class='align_center one_media broad_click @if($ads_broad['broadcaster'] === $broadcaster) active @endif'>
                            <input type="hidden" name="broadcaster" value="{{ $ads_broad['broadcaster'] }}" id="broadcaster">
                            <div><a href="{{ route('agency_campaign.step4', ['id' => $id, 'broadcaster' => $ads_broad['broadcaster'],
                                                                            'start_date' => $start_and_end_date_in_first_week['start_date_of_the_week'],
                                                                            'end_date' => $start_and_end_date_in_first_week['end_date_of_the_week']]) }}">
                                    <img src="{{ asset($ads_broad['logo'] ? $ads_broad['logo'] : '')  }}" style="margin-top: -60px;">
                                </a></div>
                            <span class="small_faint">{{ $ads_broad['boradcaster_brand'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p><br></p>

                <!-- time slots -->
                <div class="media_houses mb3 clearfix" style="margin-top: -20px;">
                    <div class="align_center col_2" style="float:left; width: 120px;">
                        <div class="time_slots">
                            <table>
                                <?php $j = 1; ?>
                                @foreach($campaign_dates_by_week as $campaign_by_week)
                                    <tr>
                                        <td>
                                            <a href="{{ route('agency_campaign.step4', ['id' => $id, 'broadcaster' => $broadcaster, 'start_date' => $campaign_by_week['start_date'], 'end_date' => $campaign_by_week['end_date']]) }}">
                                                <input type="checkbox" @if($ratecards[0]['start_date'] === $campaign_by_week['start_date']) checked @endif id="">
                                                <label id="new_client" for="week_1">Week {{ $j }} </label></a>
                                        </td>
                                    </tr>
                                    <?php $j++; ?>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="align_center col_12 " >
                        <div class="time_slots">
                            <table>
                                <thead>
                                <tr>
                                    @foreach($ratecards as $ratecard)
                                        <th style="text-align: center;">{{ $ratecard['day'] }} <br> {{ $ratecard['actual_date'] }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    @foreach($ratecards as $ratecard)
                                        <td style="font-size: 10px;">
                                            @foreach($ratecard['array_filtered_unfiltered'] as $rating)
                                                @if($rating[0]->day_id === $ratecard['day_id'])
                                                    @if(isset($rating[0]->region))
                                                        <a href="#modal_slot{{ $rating[0]->id }}" class="modal_click">
                                                            <input type="checkbox"
                                                                   @foreach($preselected_adslots as $preselected_adslot)
                                                                       @if($preselected_adslot->adslot_id === $rating[0]->id && $preselected_adslot->air_date === $ratecard['actual_date'])
                                                                            checked
                                                                       @endif
                                                                   @endforeach
                                                                   id="{{ $rating[0]->id }}">
                                                            <label id="new_client{{ $rating[0]->id }}" for="{{ $rating[0]->id }}">{{ $rating[0]->from_to_time }}</label></a>
                                                        <br>
                                                    @else
                                                            <input class="not-active" type="checkbox"
                                                                   id="">
                                                            <label style="color: white; border-color: grey; background-color: grey; pointer-events: none;" id="new_client" for="">{{ $rating[0]->from_to_time }}</label>
                                                        <br>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- proceed buttons -->
                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_4">
                        <a href="{{ route('agency_campaign.step3_2', ['id' => $id]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>

                    <div class="align_center column col_4">
                        <p class="weight_medium">Total: &#8358; {{ $total_amount ? number_format($total_amount[0]->total, 2) : '0.00' }}</p>
                        <p class="small_font"> {{ $preselected_adslots ? count($preselected_adslots) : '0' }} ad slots selected</p>
                        <?php
                        $percen_val = round((($total_amount ? $total_amount[0]->total : 0) / Session::get('first_step')->campaign_budget) * 100);
                        ?>
                        <div class="w3-border">
                            <div class="w3-grey @if($percen_val > 80) danger @else success_p @endif" style="height:24px;width:{{ $percen_val }}%"></div>
                        </div>
                        <br>
                        @if($percen_val > 80)
                            <div class="budget_div">
                                <a href="#budget" class="btn modal_click small_btn">Increase Budget</a>
                            </div>
                        @endif
                    </div>

                    <div class="column col_4 align_right">
                        <a href="{{ route('agency_campaign.checkout', ['id' => $id]) }}" class="btn uppercased _proceed ">Proceed <span class=""></span></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal_contain" id="budget">
            <div class="wallet_placer margin_center mb3"></div>
            <form method="POST" class="selsec" action="{{ route('update.budget') }}">
                {{ csrf_field() }}

                <div class="clearfix mb3">
                    <div class="input_wrap{{ $errors->has('campaign_budget') ? ' has-error' : '' }}">
                        <label class="small_faint">Campaign Budget</label>
                        <input type="number" required @if((Session::get('first_step')) != null) value="{{ (Session::get('first_step'))->campaign_budget }}" @endif name="campaign_budget" placeholder="Campaign Budget">

                        @if($errors->has('campaign_budget'))
                            <strong>
                                <span class="help-block">{{ $errors->first('campaign_budget') }}</span>
                            </strong>
                        @endif
                    </div>
                </div>
                <div class="align_right">
                    <button type="submit" class="btn">Update</button>
                </div>
            </form>
        </div>

        @foreach($adslots as $rating)
            <div class="modal_contain" style="width: 1000px;" id="modal_slot{{ $rating->id }}">
                <h2 class="sub_header mb4">{{ $rating->from_to_time }} | {{ $rating->time_difference - $rating->time_used }} Seconds Available</h2></h2>
                <form id="form_cart" action="{{ route('agency_campaign.cart') }}" method="GET">
                    {{ csrf_field() }}
                    @foreach($ratecards as $ratecard)
                        @if($ratecard['day_id'] === $rating->day_id)
                            <input type="hidden" name="air_date" id="air_date{{ $rating->id }}" value="{{ $ratecard['actual_date'] }}">
                        @endif
                    @endforeach
                    <table id="mod" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Duration</th>
                            <th>Price</th>
                            <th>Position</th>
                            {{--<th>Select</th>--}}
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $j = 1; for($i = 0; $i < count($times); $i++){ ?>
                        @if( ($rating->time_difference - $rating->time_used) >= $uploaded_data[$i]->time)
                            <tr>
                                @if($uploaded_data[$i]->file_url && $uploaded_data[$i]->channel === $rating->channels)
                                    <td>{{ $j }}</td>
                                    <td><div class="col-md-3"> <video width="150" controls><source src="{{ asset($uploaded_data[$i]->file_url) }}"></video> </div></td>
                                    <input type="hidden" name="file" class="file{{ $rating->id.$uploaded_data[$i]->id }}" value="{{ $uploaded_data[$i]->file_url }}">
                                    <td><div class="col-md-3"><span style="margin-left:15%"></span>{{ $uploaded_data[$i]->time }} Seconds</div></td>
                                    @if($uploaded_data[$i]->time === 15)
                                        <td><div class="col-md-3">&#8358;{{ $rating->price_15 }}</div></td>
                                    @elseif($uploaded_data[$i]->time === 30)
                                        <td><div class="col-md-3">&#8358;{{ $rating->price_30 }}</div></td>
                                    @elseif($uploaded_data[$i]->time === 45)
                                        <td><div class="col-md-3">&#8358;{{ $rating->price_45 }}</div></td>
                                    @elseif($uploaded_data[$i]->time === 60)
                                        <td><div class="col-md-3">&#8358;{{ $rating->price_60 }}</div></td>
                                    @endif
                                    <td>
                                        <select name="position" class="form-control" id="position{{ $rating->id.$uploaded_data[$i]->id }}">
                                            <option value="">No Position</option>
                                            @if(count($positions) > 0)
                                                @foreach($positions as $position)
                                                    <option value="{{ $position->id }}"
                                                            @foreach($ratecards as $ratecard)
                                                                @foreach($preselected_adslots as $preselected_adslot)
                                                                    @if($preselected_adslot->adslot_id === $rating->id && $preselected_adslot->air_date == $ratecard['actual_date'])
                                                                        @if((int)$preselected_adslot->time === (int)$uploaded_data[$i]->time)
                                                                            @if($preselected_adslot->filePosition_id === $position->id)
                                                                                selected
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endforeach
                                                    >{{ $position->position }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>

                                    <td class="pick_button{{ $rating->id.$uploaded_data[$i]->id }}"><button id="button{{ $rating->id.$uploaded_data[$i]->id }}"
                                                                                                            @foreach($ratecards as $ratecard)
                                                                                                                @foreach($preselected_adslots as $preselected_adslot)
                                                                                                                    @if($preselected_adslot->adslot_id === $rating->id && $preselected_adslot->air_date === $ratecard['actual_date'])
                                                                                                                        @if((int)$preselected_adslot->time === (int)$uploaded_data[$i]->time)
                                                                                                                            class="btn-disable"
                                                                                                                        @endif
                                                                                                                    @endif
                                                                                                                @endforeach
                                                                                                            @endforeach
                                                                                                    type="button"
                                                                                                    data-file_slot="{{ $rating->id.$uploaded_data[$i]->id }}"
                                                                                                    @if($uploaded_data[$i]->time === 15)
                                                                                                    data-price="{{ $rating->price_15 }}"
                                                                                                    @elseif($uploaded_data[$i]->time === 30)
                                                                                                    data-price="{{ $rating->price_30 }}"
                                                                                                    @elseif($uploaded_data[$i]->time === 45)
                                                                                                    data-price="{{ $rating->price_45 }}"
                                                                                                    @elseif($uploaded_data[$i]->time === 60)
                                                                                                    data-price="{{ $rating->price_60 }}"
                                                                                                    @endif
                                                                                                    data-adslot_id="{{ $rating->id }}"
                                                                                                    data-range="{{ $rating->from_to_time }}"
                                                                                                    data-time="{{ $uploaded_data[$i]->time }}"
                                                                                                    data-walkin="{{ $id }}"
                                                                                                    data-file="{{ $uploaded_data[$i]->file_url }}"
                                                                                                    data-file_name="{{ $uploaded_data[$i]->file_name }}"
                                                                                                    data-file_code="{{ $uploaded_data[$i]->file_code }}"
                                                                                                    data-file_format="{{ $uploaded_data[$i]->format }}"
                                                                                                    data-broadcaster="{{ $broadcaster }}"
                                                                                                    data-target="bs-example-modal-lg{{ $rating->id }}"
                                                                                                    class="btn btn-success btn-xs saveCart">select</button></td>

                                @endif
                            </tr>
                        @endif
                        <?php $j++; } ?>
                        </tbody>
                    </table>

                </form>
            </div>
        @endforeach

    </div>
@stop

@section('scripts')
    <script>
        $("body").delegate(".saveCart", "click", function() {

            $(".modal_container").css({
                opacity: 0.5
            });

            $('.saveCart').attr("disabled", false);
            var file_slot = $(this).data('file_slot');
            var price = $(this).data('price');
            var adslot_id = $(this).data('adslot_id');
            var range = $(this).data('range');
            var time = $(this).data('time');
            var walkins = $(this).data('walkin');
            var url1 = $("#form_cart").attr("action");
            var position = $("select#position" + file_slot).val();
            var file = $(this).data('file');
            var file_name = $(this).data('file_name');
            var file_code = $(this).data('file_code');
            var file_format = $(this).data('file_format');
            var broadcaster = $(this).data("broadcaster");
            var air_date = $("#air_date"+adslot_id).val();
            var target = $(this).data('target');
            $(".saveCart").attr('disabled', true);

            $.ajax({
                url: url1,
                method: "GET",
                data: {
                    price: price,
                    adslot_id: adslot_id,
                    file: file,
                    file_name: file_name,
                    file_code: file_code,
                    range: range,
                    broadcaster: broadcaster,
                    time: time,
                    position: position,
                    walkins: walkins,
                    air_date: air_date,
                    file_format: file_format,
                    '_token': $('input[name=_token]').val()
                },
                success: function (data) {
                    if (data.success === "success") {
                        $(".modal_container").css({
                            opacity: 1
                        });
                        toastr.success('File picked successfully');
                        // $(".saveCart").attr('disabled', false);
                        $("#button" + file_slot).attr('disabled', true);
                        location.reload();
                        $("#rate_this" + adslot_id).addClass('choosen');
                        // $('#cart_item').load(location.href + ' #cart_item');
                    } else if (data.error === "error") {
                        $(".modal_container").css({
                            opacity: 1
                        });
                        toastr.error('You have already selected this position, please select another one');
                        $(".saveCart").attr('disabled', false);
                        // location.reload();
                    } else if (data.file_error === "file_error") {
                        $(".modal_container").css({
                            opacity: 1
                        });
                        toastr.error('This position isnt available, please select another position');
                        $(".saveCart").attr('disabled', false);
                    }else if(data.budget_exceed_error === "budget_exceed_error"){
                        $(".modal_container").css({
                            opacity: 1
                        });
                        toastr.error('Your total amount has exceeded your budget, please increase the budget');
                        // $(".budget_div").show();
                        // $(".progress").addClass('danger');
                        $(".saveCart").attr('disabled', false);
                    }else {
                        $(".modal_container").css({
                            opacity: 1
                        });
                        toastr.error('An error occurred while selecting this slot');
                        $(".saveCart").attr('disabled', false);
                        return
                    }
                }
            });
        });



    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        .danger  {
            background-color: red!important;
        }
        .success_p {
            background-color: green!important;
        }
        .time_slots {
            height: 400px;
            !important;
        }
    </style>
@stop


