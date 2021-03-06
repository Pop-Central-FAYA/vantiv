@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Checkout</title>
@stop

@section('content')
    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create New Campaign</h2>
            </div>
        </div>


        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color container_modal_pay">

            <div class="margin_center col_10 clearfix pt4 create_fields">

                <!-- progress bar -->
                <div class="create_gauge">
                    <div class=""></div>
                </div>


                <p class='weight_medium m-b'>Booked Ad Spots</p>
                <p class="small_faint col_9 mb4"></p>


                <table class="mb border_bottom">
                    <tr>
                        <th>Time Slots</th>
                        <th>Ad Length</th>
                        <th>Price</th>
                        <th>Surge</th>
                        <th>Position</th>
                        <th>Total</th>
                        <th>Air Date</th>
                        <th></th>
                    </tr>
                    @foreach($preselected_adslot_arrays as $preselected_adslot_array)
                        <tr>
                            <td>{{ $preselected_adslot_array['from_to_time'] }}</td>
                            <td>{{ $preselected_adslot_array['time'] }} seconds</td>
                            <td>&#8358; {{ number_format($preselected_adslot_array['price'], 2) }}</td>
                            <td>{{ $preselected_adslot_array['percentage'] }}%</td>
                            <td>{{ $preselected_adslot_array['position'] }}</td>
                            <td>&#8358; {{ number_format($preselected_adslot_array['total_price'], 2) }}</td>
                            <td>{{ date('l, jS F, Y', strtotime($preselected_adslot_array['air_date'])) }}</td>
                            <td><a href="#delete_cart{{ $preselected_adslot_array['id'] }}" class="color_red close_red modal_click"><span class="_icon"></span> Remove</a></td>
                        </tr>
                    @endforeach
                </table>


                <div class="clearfix weight_medium _total_amount">
                    <p class="left">Total:</p>
                    <p class="left">&#8358; {{ number_format($calc[0]->total_price, 2) }}</p>
                </div>

                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_6">
                        <a href="{{ route('campaign.create4', ['id' => $id, 'broadcaster' => $broadcaster, 'start_date' => current($campaign_dates_for_first_week), 'end_date' => end($campaign_dates_for_first_week)]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>

                    <div class="column col_6 align_right">
                        <a href="#payment" class="btn uppercased _proceed modal_click">Proceed <span class=""></span></a>
                    </div>
                </div>
                <p><br></p>
                {{ $preselected_adslot_arrays->links('pagination.general') }}
            </div>
        </div>
        <!-- main frame end -->

    </div>

    <!-- are you sure modal -->
    @foreach($preselected_adslot_arrays as $preselected_adslot_array)
        <div class="modal_contain" id="delete_cart{{ $preselected_adslot_array['id'] }}">
            <div class="wallet_placer margin_center mb3"></div>

            <p class="align_center margin_center col_10 mb4">Are you sure you want to delete this item ?</p>

            <div class="align_right">
                <a href="{{ route('cart.remove', ['id' => $preselected_adslot_array['id']]) }}" class="btn">Delete</a>
            </div>

        </div>
    @endforeach

    <!-- payment modal -->
    <div class="modal_contain payment_modal container_modal_pay" id="payment">
        <h2 class="border_bottom align_center">Complete Purchase</h2>

        <div class="padd mb4 pt">
            <form method="POST" action="{{ route('submit.campaign', ['id' => $id]) }}">
                {{ csrf_field() }}
                <h3 class="weight_medium uppercased">Your campaign will be created in the "ON HOLD" mode, please review and submit to the broadcaster in order to start processing</h3>
                <p class="small_faint mb4"></p>
                <input type="hidden" value="{{ $calc[0]->total_price }}" name="total"/>
                <p><br></p>
                <div class="mb4 create_payment">
                    <div class="column align_right card_transfer" >
                        <button type="submit" class="btn uppercased _proceed">Proceed <span class=""></span></button>
                    </div>
                </div>
            </form>
        </div>

        <br>
        <br>
    </div>
    <!-- end -->
@stop




