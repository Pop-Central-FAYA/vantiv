@extends('layouts.new_app')

@section('title')
    <title>Create Campaign</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Payment</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Create Campaign</a></li>
                        <li><a href="#">Summary</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">

                @if(count($query) === 0)

                    <p>You have 0 item in your cart</p>

                @else

                    <div class="Add-brand">
                        <h2>Summary</h2>
                        <form class="campform">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><b>Campaign Name:</b> {{ $first_session->name }}</p>
                                    <p><b>Brand Name:</b> {{ $brand[0]->name }}</p>
                                    <p><b>Product Name:</b> {{ $first_session->product }}</p>
                                    <p><b>Date:</b> {{ $first_session->start_date }} - {{ $first_session->end_date }}</p>
                                </div>

                                <div class="col-md-6">
                                    <p> <b><i class="fa fa-users"></i> Day Parts: </b>
                                        @foreach ($day_part as $daypart)
                                            {{ $daypart->day_parts }}
                                        @endforeach
                                    </p>
                                    <p>
                                        <b><i class="fa fa-user"></i>Viewers age</b>
                                        {{ $first_session->min_age }} - {{ $first_session->max_age }} years
                                    </p>
                                    <p>
                                        <b><i class="fa fa-map-marker" aria-hidden="true"></i> Region</b>
                                        @foreach ($region as $regions)
                                            {{ $regions->region }}
                                        @endforeach
                                    </p>
                                </div>
                            </div>


                            <div class="row" style="margin-top: 20px;">
                                <h2 style="margin-bottom: 20px;">Uploaded list</h2>

                                <table class="table table-hover" style="font-size:16px">
                                    <tr>
                                        <th>ID</th>
                                        <th>Time</th>
                                        <th>Duration</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    @foreach($query as $queries)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $queries->from_to_time }}</td>
                                            <td>{{ $queries->time }} seconds</td>
                                            <td>&#8358;{{ number_format($queries->price, 2) }}</td>
                                            <td>
                                                <a href="{{ route('agency_cart.remove', ['id' => $queries->adslot_id]) }}" style="font-size: 16px">
                                                    <span class="label label-danger">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                        Remove
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>

                                <h3 style="padding: 0;">
                                    &#8358;{{ number_format($calc[0]->total_price, 2) }}
                                </h3>

                            </div>

                        </form>
                    </div>

                    <div class="container">
                        <p align="right">
                            <button id="step7" class="btn campaign-button btn-danger btn-lg" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>
                            <button class="btn campaign-button btn-danger btn-lg" style="margin-right:15%" data-toggle="modal" data-target=".bs-example2-modal-lg" >Create Campaign <i class="fa fa-play" aria-hidden="true"></i></button>
                        </p>

                        <div class="modal fade bs-example2-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" style="padding: 5%">
                                    <div class="modal-body">
                                        <h2>Payment</h2>

                                        <form method="POST" action="{{ route('broadcaster.user.submit.campaign', ['walkins' => $walkins, 'broadcaster' => $broadcaster, 'broadcaster_user' => $broadcaster_user]) }}">
                                            {{ csrf_field() }}
                                            <div class="modal-body">
                                                for the time-slot bought for your adverts, the price is: <br />
                                                <h3> Total: &#8358;{{ number_format($calc[0]->total_price, 2) }} </h3>

                                                Choose payment plan: <br>
                                                <input type="radio" name="pay" id="pay" value="Cash" checked> Cash<br>
                                                <input type="radio" name="pay" id="pay" value="Card"> Card<br>
                                                <input type="radio" name="pay" id="pay" value="Transfer"> Transfer
                                                <input type="hidden" value="{{ $calc[0]->total_price }}" name="total" />

                                            </div>

                                            <div class="modal-footer card_transfer">
                                                <p align="center">
                                                    <button type="submit" class="btn btn-large" style="background: #34495e; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Confirm payment</button>

                                                </p>
                                            </div>
                                        </form>

                                        <form id="fund-form" role='form' action="{{ route('broadcaster.user.pay', ['walkins' => $walkins, 'broadcaster' => $broadcaster, 'broadcaster_user' => $broadcaster_user]) }}" method="POST">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input id="amount" type="hidden" name="amount" readonly value="{{ $calc[0]->total_price }}" class="form-control">
                                            <input type="hidden" name="email" id="email" value="{{ $user[0]->email }}">
                                            <input type="hidden" name="name" id="name" value="{{ $user[0]->firstname .' '.$user[0]->lastname }}">
                                            <input type="hidden" name="phone_number" id="phone_number" value="{{ $user[0]->phone_number }}">
                                            <input type="hidden" name="reference" id="reference" value="" />
                                            <input type="hidden" name="user_id" value="{{ $walkins }}" />
                                            <div class="modal-footer card_type" style="display: none;">
                                                <p align="center">
                                                    <button type="button" onclick="payWithPaystack()" class="btn btn-large" style="background: #34495e; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Pay with your card</button>
                                                </p>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script src="{{ asset('asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>

    <!-- bootstrap datepicker -->
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>

    <!-- date-range-picker -->
    <script src="{{ 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js' }}"></script>
    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('asset/plugins/iCheck/icheck.min.js') }}"></script>

    <!-- bootstrap color picker -->
    <script src="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

    <!-- bootstrap time picker -->
    <script src="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>

    <script>
        $(document).ready(function() {
            var user_id = "<?php echo $walkins ?>";
            var broadcaster = "<?php echo $broadcaster ?>";
            var broadcaster_user = "<?php echo $broadcaster_user ?>";
            $('#step7').click(function(){
                window.location.href = '/broadcaster-user/campaign/create/'+user_id+'/'+broadcaster+'/'+broadcaster_user+'/step5';
            });
        });
    </script>

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
    </script>

    <script>
        function payWithPaystack(){
            $(".container").css({
                opacity: 0.5
            });
            var handler = PaystackPop.setup({
                key: 'pk_test_9945d2a543e97e34d0401f1d926e79dc1716ccc7',
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
                    alert('window closed');
                }
            });
            handler.openIframe();
        }
    </script>


@endsection