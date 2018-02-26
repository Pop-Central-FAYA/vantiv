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
                                        @foreach($day_part as $daypart)
                                            {{ $daypart->day_parts }}
                                        @endforeach
                                    </p>
                                    <p>
                                        <b><i class="fa fa-user"></i>Viewers age</b>
                                        {{ $first_session->min_age }} - {{ $first_session->max_age }} years
                                    </p>
                                    <p>
                                        <b><i class="fa fa-map-marker" aria-hidden="true"></i> Region</b>
                                        @foreach($region as $regions)
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
                                        <th>Media Station</th>
                                        <th>Time</th>
                                        <th>Duration</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    @foreach($query as $queries)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td> <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="5%">   </td>
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
                            <button id="step7" class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>
                            <button class="btn campaign-button" style="margin-right:15%" data-toggle="modal" data-target=".bs-example2-modal-lg" >Create Campaign <i class="fa fa-play" aria-hidden="true"></i></button>
                        </p>

                        <div class="modal fade bs-example2-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" style="padding: 5%">
                                    <div class="modal-body">
                                        <h2>Payment</h2>

                                        <form method="POST" action="{{ route('submit.campaign', ['walkins' => $walkins]) }}">
                                            {{ csrf_field() }}
                                            <div class="modal-body">
                                                for the time-slot bought for your adverts, the price is: <br />
                                                <h3> Total: &#8358;{{ number_format($calc[0]->total_price, 2) }} </h3>

                                                Choose payment plab:
                                                <input type="radio" name="payment" value="Cash" checked> Cash<br>
                                                <input type="radio" name="payment" value="Payment"> Cash<br>
                                                <input type="radio" name="payment" value="other"> Transfer
                                                <input type="hidden" value="{{ $calc[0]->total_price }}" name="total" />

                                            </div>

                                            <div class="modal-footer">
                                                <p align="center">
                                                    <button type="submit" class="btn btn-large" style="background: #34495e; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Confirm payment</button>
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

    <script>
        $(document).ready(function(){
            $("#step7").click(function() {
                window.location.href = "/campaign/create/1/step7";
            });
        });
    </script>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();

            //Datemask dd/mm/yyyy
            $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
            //Datemask2 mm/dd/yyyy
            $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
            //Money Euro
            $("[data-mask]").inputmask();

            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
            //Date range as a button
            $('#daterange-btn').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }
            );

            //Date picker
            $('#datepicker').datepicker({
                autoclose: true
            });

            $('#datepickerend').datepicker({
                autoclose: true
            });

            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            //Red color scheme for iCheck
            $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
                checkboxClass: 'icheckbox_minimal-red',
                radioClass: 'iradio_minimal-red'
            });
            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            //Colorpicker
            $(".my-colorpicker1").colorpicker();
            //color picker with addon
            $(".my-colorpicker2").colorpicker();

            //Timepicker
            $(".timepicker").timepicker({
                showInputs: false
            });
        });
    </script>

@endsection