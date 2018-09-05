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

            <div class="margin_center col_7 clearfix pt4 create_fields">

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
                        <th></th>
                    </tr>
                    @foreach($queries as $query)
                        <tr>
                            <td>{{ $query['from_to_time'] }}</td>
                            <td>{{ $query['time'] }} seconds</td>
                            <td>&#8358; {{ number_format($query['price'], 2) }}</td>
                            <td>{{ $query['percentage'] }}%</td>
                            <td>{{ $query['position'] }}</td>
                            <td>&#8358; {{ number_format($query['total_price'], 2) }}</td>
                            <td><a href="#delete_cart{{ $query['id'] }}" class="color_red close_red modal_click"><span class="_icon"></span> Remove</a></td>
                        </tr>
                    @endforeach
                </table>


                <div class="clearfix weight_medium _total_amount">
                    <p class="left">Total:</p>
                    <p class="left">&#8358; {{ number_format($calc[0]->total_price, 2) }}</p>
                </div>

                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_6">
                        <a href="{{ route('campaign.create4', ['id' => $id, 'broadcaster' => $broadcaster]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>

                    <div class="column col_6 align_right">
                        <a href="#payment" class="btn uppercased _proceed modal_click">Proceed <span class=""></span></a>
                    </div>
                </div>

            </div>
        </div>
        <!-- main frame end -->

    </div>

    <!-- are you sure modal -->
    @foreach($queries as $query)
        <div class="modal_contain" id="delete_cart{{ $query['id'] }}">
            <div class="wallet_placer margin_center mb3"></div>

            <p class="align_center margin_center col_10 mb4">Are you sure you want to delete this item ?</p>

            <div class="align_right">
                <a href="{{ route('cart.remove', ['id' => $query['id']]) }}" class="btn">Delete</a>
            </div>

        </div>
    @endforeach

    <!-- payment modal -->
    <div class="modal_contain payment_modal container_modal_pay" style="height: 600px;" id="payment">
        <h2 class="border_bottom align_center">Complete Purchase</h2>

        <div class="padd mb4 pt">
            <h3 class="weight_medium uppercased">TOTAL: &#8358; {{ number_format($calc[0]->total_price, 2) }}</h3>
            <p class="small_faint mb4"></p>

            <p class="mb">Choose Payment Option</p>
            <form method="POST" action="{{ route('submit.campaign', ['id' => $id]) }}">
                {{ csrf_field() }}

                <input type="hidden" value="{{ $calc[0]->total_price }}" name="total"/>
                <div class="mb4 create_payment">
                    <li class="m-b">
                        <input type="radio" name="pay" value="Cash" id="cash">
                        <label class="weight_medium" for="cash">Cash</label>
                    </li>

                    <li class="m-b">
                        <input type="radio" name="pay" value="Card" id="card">
                        <label class="weight_medium" for="card">Card</label>
                    </li>

                    <li class="">
                        <input type="radio" name="pay" value="Transfer" id="trans">
                        <label class="weight_medium" for="trans">Transfer</label>
                    </li>
                    <br>
                    <div class="column align_right card_transfer" style="display: none;">
                        <button type="submit" class="btn uppercased _proceed">Proceed <span class=""></span></button>
                    </div>
                </div>
            </form>
            <form id="fund-form" role='form' action="{{ route('broadcaster.pay') }}" method="POST">
                <div class="mb4 create_payment">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input id="amount" type="hidden" name="amount" readonly value="{{ $calc[0]->total_price }}" class="form-control">
                    <input type="hidden" name="email" id="email" value="{{ $user[0]->email }}">
                    <input type="hidden" name="name" id="name" value="{{ $user[0]->firstname .' '.$user[0]->lastname }}">
                    <input type="hidden" name="phone_number" id="phone_number" value="{{ $user[0]->phone_number }}">
                    <input type="hidden" name="reference" id="reference" value="" />
                    <input type="hidden" name="user_id" value="{{ $id }}" />
                    <div class="column align_right card_type" style="display: none;">
                        <button type="button" onclick="payWithPaystack()" class="btn uppercased _proceed">Pay with your card</button>
                    </div>
                </div>
            </form>
        </div>

        <br>
        <br>
    </div>
    <!-- end -->
@stop

@section('scripts')
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>

        $(document).ready(function() {

            $('input[name=pay]').change(function(){
                var value = $( 'input[name=pay]:checked' ).val();
                if(value === 'Card'){
                    $('.card_type').show();
                    $('.card_transfer').hide();
                }else{
                    $(".card_transfer").show();
                    $(".card_type").hide();
                }
            });
        });

        function payWithPaystack(){
            $(".container_modal_pay").css({
                opacity: 0.5
            });
            $("#payment").fadeOut(1000);
            var handler = PaystackPop.setup({
                key: "<?php echo getenv('PK_TEST_KEY'); ?>",
                email: "<?php echo $user[0]->email; ?>",
                amount: parseFloat(document.getElementById('amount').value * 100),
                metadata: {
                    custom_fields: [
                        {
                            display_name: "<?php echo $user[0]->firstname .' '.$user[0]->lastname; ?>",
                            value: "<?php echo $user[0]->phone_number; ?>"
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
                    $("#payment").fadeIn(1000);
                    // alert('window closed');
                }
            });
            handler.openIframe();
        }
    </script>
@stop



