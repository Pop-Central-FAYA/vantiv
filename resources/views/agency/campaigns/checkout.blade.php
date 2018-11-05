@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Checkout</title>
@stop

@section('content')
    <!-- main container -->
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

            <div class="margin_center col_10 clearfix pt4 create_fields container_modal_pay">

                <!-- progress bar -->
                <div class="create_gauge">
                    <div class=""></div>
                </div>


                <p class='weight_medium m-b'>Booked Ad Spots</p>
                <p class="small_faint col_9 mb4"></p>


                <table class="mb border_bottom">
                    <tr>
                        <th>Channel</th>
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
                            <td>{{ $preselected_adslot_array['broadcaster_brand'] }}</td>
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
                    <div class="column col_4">
                        <a href="{{ route('agency_campaign.step3_2', ['id' => $id]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>
                    <div class="column col_4">
                        @if($wallet_balance)
                            @if((int)$wallet_balance[0]->current_balance < (int)$calc[0]->total_price)
                                <a href="#fund_wallet" class="btn modal_click small_btn"><span class="_plus"></span>Fund Wallet</a>
                            @endif
                        @else
                            <a href="#fund_wallet" class="btn modal_click small_btn"><span class="_plus"></span>Create Wallet</a>
                        @endif
                        <p><br></p>
                    </div>
                    <div class="column col_4 align_right">
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
                    <a href="{{ route('agency_cart.remove', ['id' => $preselected_adslot_array['id']]) }}" class="btn">Delete</a>
                </div>

        </div>
    @endforeach

    <!-- payment modal -->
    <div class="modal_contain payment_modal" id="payment">
        <h2 class="border_bottom align_center">Complete Purchase</h2>

        <div class="padd mb4 pt">
            <h3 class="weight_medium uppercased">TOTAL: &#8358; {{ number_format($calc[0]->total_price, 2) }}</h3>
            <p class="small_faint mb4"></p>

            <form method="POST" action="{{ route('agency_submit.campaign', ['id' => $id]) }}">
                {{ csrf_field() }}
                <h3 class="weight_medium uppercased">Your campaign will be created in the "ON HOLD" mode, please review and submit to the broadcaster in order to start processing</h3>
                <input type="hidden" value="{{ $calc[0]->total_price }}" name="total"/>
                <div class="mb4 create_payment">
                    <br>
                    <div class="column align_right">
                        <button type="submit" class="btn uppercased _proceed">Proceed <span class=""></span></button>
                    </div>
                </div>
            </form>
        </div>

        <br>
        <br>
    </div>
    <!-- end -->
    {{--updating wallets--}}
    <!-- fund wallet modal -->
    <div class="modal_contain payment_wallet" id="fund_wallet">

        <h2 class="sub_header mb4 align_center">Fund Wallet</h2>

        <form id="fund-form" role='form' action="{{ route('pay') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <script src="https://js.paystack.co/v1/inline.js"></script>
            <div class="input_wrap">
                <label class="small_faint uppercased weight_medium">Amount</label>
                <input id="amount" type="number" name="amount" placeholder="Enter Amount">
            </div>
            <input type="hidden" name="email" id="email" value="{{ Auth::user()->email }}">
            <input type="hidden" name="name" id="name" value="{{ Auth::user()->first_name .' '.Auth::user()->last_name }}">
            <input type="hidden" name="phone_number" id="phone_number" value="{{ Auth::user()->phone }}">
            <input type="hidden" name="reference" id="reference" value="" />
            <input type="hidden" name="user_id" value="{{ $agency_id }}" />
        </form>

        <div class="mb4">
            <input type="button" value="Fund Wallet" id="fund" onclick="payWithPaystack()" class="full btn uppercased">
        </div>

    </div>
@stop

@section('scripts')
    <script>
        // $(document).ready(function () {
            function payWithPaystack(){
                $(".container_modal_pay").css({
                    opacity: 0.5
                });
                $(".payment_wallet").fadeOut(1000);
                var handler = PaystackPop.setup({
                    key: "<?php echo getenv('PAYSTACK_PUBLIC_KEY'); ?>",
                    email: "<?php echo Auth::user()->email; ?>",
                    amount: parseFloat(document.getElementById('amount').value * 100),
                    metadata: {
                        custom_fields: [
                            {
                                display_name: "<?php echo Auth::user()->first_name .' '.Auth::user()->last_name; ?>",
                                value: "<?php echo Auth::user()->phone; ?>"
                            }
                        ]
                    },
                    callback: function(response){
                        document.getElementById('reference').value = response.reference;
                        document.getElementById('fund-form').submit();
                    },
                    onClose: function(){
                        $(".container_modal_pay").css({
                            opacity: 1
                        });
                        $(".payment_wallet").fadeIn(1000);
                    }
                });
                handler.openIframe();
            }
        // })
    </script>
@stop



