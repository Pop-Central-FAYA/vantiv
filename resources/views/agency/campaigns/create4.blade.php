@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Campaign Step 5 </title>
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

            <div class="margin_center col_7 clearfix pt4 create_fields">

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
                <div class="media_houses mb3 clearfix">
                    @foreach($ads_broads as $ads_broad)
                        <div class='align_center one_media broad_click @if($ads_broad['broadcaster'] === $broadcaster) active @endif'>
                            <input type="hidden" name="broadcaster" value="{{ $ads_broad['broadcaster'] }}" id="broadcaster">
                            <div><a href="{{ route('agency_campaign.step4', ['id' => $id, 'broadcaster' => $ads_broad['broadcaster']]) }}"><img src="{{ asset($ads_broad['logo'] ? decrypt($ads_broad['logo']) : '')  }}"></a></div>
                            <span class="small_faint">{{ $ads_broad['broadcaster_brand'] }}</span>
                        </div>
                    @endforeach
                </div>


                <!-- time slots -->
                <div class="time_slots">
                    <table>
                        @foreach($ratecards as $ratecard)
                        <tr>
                            <th>{{ $ratecard['day'] }}</th>
                            <th>{{ $ratecard['hourly_range'] }}</th>
                            @foreach($ratecard['adslot'] as $rating)
                                <td>
                                    <a href="#modal_slot{{ $rating->id }}" class="modal_click">
                                    <input type="checkbox"
                                           @foreach($cart as $carts)
                                           @if($carts->adslot_id === $rating->id)
                                           checked
                                           @endif
                                           @endforeach

                                           id="{{ $rating->id }}">
                                    <label id="new_client{{ $rating->id }}" for="{{ $rating->id }}">{{ $rating->from_to_time }}</label></a>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach

                    </table>
                </div>


                <!-- proceed buttons -->
                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_4">
                        <a href="{{ route('agency_campaign.step3_2', ['id' => $id]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>

                    <div class="align_center column col_4">
                        <p class="weight_medium">Total: &#8358; {{ $total_amount ? number_format($total_amount[0]->total, 2) : '0.00' }}</p>
                        <p class="small_font"> {{ $cart ? count($cart) : '0' }} ad slots selected</p>
                    </div>

                    <div class="column col_4 align_right">
                        <a href="{{ route('agency_campaign.checkout', ['id' => $id]) }}" class="btn uppercased _proceed ">Proceed <span class=""></span></a>
                    </div>
                </div>

            </div>
        </div>

        {{--{{ dd($ratecards) }}--}}
        @foreach($ratecards as $ratecard)
            @foreach($ratecard['adslot'] as $rating)
                <div class="modal_contain" id="modal_slot{{ $rating->id }}">
                    <h2 class="sub_header mb4">{{ $rating->from_to_time }} | {{ $rating->time_difference - $rating->time_used }} Seconds Available</h2></h2>
                    <form id="form_cart" action="{{ route('agency_campaign.cart') }}" method="GET">
                        {{ csrf_field() }}
                        <?php
                        $select_price = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPercentages where adslot_id = '$rating->id'");
                        if(!$select_price){
                            $select_price = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPrices where adslot_id = '$rating->id'");
                        }
                        ?>


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

                            <?php $j = 1; $i = 0; for($i = 0; $i < count($times); $i++){ ?>
                            @if( ($rating->time_difference - $rating->time_used) >= $datas[$i]->time)
                                <tr>
                                    @if($datas[$i]->uploads && $datas[$i]->channel === $rating->channels)

                                        <td>{{ $j }}</td>
                                        <td><div class="col-md-3"> <video width="150" controls><source src="{{ asset(decrypt($datas[$i]->uploads)) }}"></video> </div></td>
                                        <input type="hidden" name="file" class="file{{ $rating->id.$datas[$i]->id }}" value="{{ $datas[$i]->uploads }}">
                                        <td><div class="col-md-3"><span style="margin-left:15%"></span>{{ $datas[$i]->time }} Seconds</div></td>
                                        @if($datas[$i]->time === 15)
                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_15 }}</div></td>
                                        @elseif($datas[$i]->time === 30)
                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_30 }}</div></td>
                                        @elseif($datas[$i]->time === 45)
                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_45 }}</div></td>
                                        @elseif($datas[$i]->time === 60)
                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_60 }}</div></td>
                                        @endif
                                        <td>
                                            <select name="position" class="form-control" id="position{{ $rating->id.$datas[$i]->id }}">
                                                <option value="">No Position</option>
                                                @if(count($positions) > 0)
                                                    @foreach($positions as $position)
                                                        <option value="{{ $position->id }}"
                                                                @foreach($cart as $ca)
                                                                @if($ca->adslot_id === $rating->id)
                                                                @if((int)$ca->time === (int)$datas[$i]->time)
                                                                @if($ca->filePosition_id === $position->id)
                                                                selected
                                                                @endif
                                                                @endif
                                                                @endif
                                                                @endforeach
                                                        >{{ $position->position }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>

                                        <td class="pick_button{{ $rating->id.$datas[$i]->id }}"><button id="button{{ $rating->id.$datas[$i]->id }}"
                                                                                                        @foreach($cart as $ca)
                                                                                                        @if($ca->adslot_id === $rating->id)
                                                                                                        @if((int)$ca->time === (int)$datas[$i]->time)
                                                                                                        disabled
                                                                                                        @endif
                                                                                                        @endif
                                                                                                        @endforeach
                                                                                                        type="button"
                                                                                                        data-file_slot="{{ $rating->id.$datas[$i]->id }}"
                                                                                                        @if($datas[$i]->time === 15)
                                                                                                        data-price="{{ $select_price[0]->price_15 }}"
                                                                                                        @elseif($datas[$i]->time === 30)
                                                                                                        data-price="{{ $select_price[0]->price_30 }}"
                                                                                                        @elseif($datas[$i]->time === 45)
                                                                                                        data-price="{{ $select_price[0]->price_45 }}"
                                                                                                        @elseif($datas[$i]->time === 60)
                                                                                                        data-price="{{ $select_price[0]->price_60 }}"
                                                                                                        @endif
                                                                                                        data-adslot_id="{{ $rating->id }}"
                                                                                                        data-range="{{ $rating->from_to_time }}"
                                                                                                        data-time="{{ $datas[$i]->time }}"
                                                                                                        data-walkin="{{ $id }}"
                                                                                                        data-file="{{ $datas[$i]->uploads }}"
                                                                                                        data-file_name="{{ $datas[$i]->file_name }}"
                                                                                                        data-file_code="{{ $datas[$i]->file_code }}"
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
            var broadcaster = $(this).data("broadcaster");
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
                    } else {
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



