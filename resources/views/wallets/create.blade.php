@extends('layouts.new_app')
@section('title')
    <title>{{ $agency_id ? 'Agency' : 'Advertiser'}}  - Credit Wallet</title>
@stop
@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Credit Wallet </h1>
                    <ul>
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-edit"></i>{{ $agency_id ? 'Agency' : 'Advertiser'}}</a></li>
                        <li><a href="#">Credit Wallet </a></li>
                    </ul>
                </div>
                <div class="col-12 Wallet-Credit">
                    <div class="card-type">
                        <h2>
                            Click to credit wallet
                            <a href="#" class="btn btn-success btn-lg" data-toggle="modal" data-target="#myModal">
                                <!-- <img src="{{ asset('new_assets/images/paypal-logo.png') }}" alt=""> -->
                                Pay
                            </a>
                        </h2>
                        @if (count($wallet) === 0)
                            <h3>Current Balance : <a href="#">&#8358;0.00</a></h3></div>
                        @else
                            <h3>Current Balance : <a href="#">&#8358;{{ number_format($wallet[0]->balance, 2) }}</a></h3></div>
                        @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content container">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Fund Wallet</h4>
                </div>
                <div class="modal-body">

                    <form id="fund-form" role='form' action="{{ route('pay') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <script src="https://js.paystack.co/v1/inline.js"></script>
                        <h4>Please Enter an Amount</h4>
                        <input id="amount" type="number" name="amount" class="form-control">
                        <input type="hidden" name="email" id="email" value="{{ $user_det[0]->email }}">
                        <input type="hidden" name="name" id="name" value="{{ $user_det[0]->firstname .' '.$user_det[0]->lastname }}">
                        <input type="hidden" name="phone_number" id="phone_number" value="{{ $user_det[0]->phone_number }}">
                        <input type="hidden" name="reference" id="reference" value="" />
                        <input type="hidden" name="user_id" value="{{ $user_id }}" />
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btmn-default" data-dismiss="modal">Exit</button>
                    <button type="button" class="btn btn-primary" onclick="payWithPaystack()">Fund</button>
                </div>
            </div>
        </div>
    </div>

    {{--old one--}}


@stop
@section('scripts')
    <!-- Select2 -->
    <script src="{{ asset('agency_asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset('agency_asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('agency_asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('agency_asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <!-- bootstrap time picker -->
    <script src="{{ asset('agency_asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="{{ asset('agency_asset/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('agency_asset/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('agency_asset/plugins/fastclick/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('agency_asset/dist/js/app.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('agency_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script>
        function payWithPaystack(){
            $(".container").css({
                opacity: 0.5
            });
            var handler = PaystackPop.setup({
                key: 'pk_test_9945d2a543e97e34d0401f1d926e79dc1716ccc7',
                email: "<?php echo $user_det[0]->email; ?>",
                amount: parseFloat(document.getElementById('amount').value * 100),
                metadata: {
                    custom_fields: [
                        {
                            display_name: "<?php echo $user_det[0]->firstname .' '.$user_det[0]->lastname; ?>",
                            value: "<?php echo $user_det[0]->phone_number; ?>"
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
@stop
