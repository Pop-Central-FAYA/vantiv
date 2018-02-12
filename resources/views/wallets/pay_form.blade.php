@extends('agency_layouts.app')
@section('title')
    <title>Agency | Credit Wallet</title>
@stop
@section('content')

    <section class="content-header">
        <h1>
            Credit Wallet

        </h1>
        <hr/>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Agency</a> </li>
            <li><a href=""><i class="fa fa-address-card"></i> Credit Wallet</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <div class="col-md-8 Campaign" style="padding:2%">

                <div class="row">

                    <div class="panel-body">
                        <form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
                            <div class="row" style="margin-bottom:40px;">
                                <div class="col-md-8 col-md-offset-2">
                                    <p>
                                    <div>
                                        Credit Wallet with
                                        ₦ {{ number_format($amount, 2) }}
                                    </div>
                                    </p>
                                    <?php
                                    $agency_id = Session::get('agency_id');
                                    $user_id = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT user_id from agents where id = '$agency_id'");
                                    $user = $user_id[0]->user_id;
                                    $user_det = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from users where id = '$user'");
                                    ?>
                                    <input type="hidden" name="email" value="{{ $user_det[0]->email }}" > {{-- required --}}
                                    <input type="hidden" name="amount" value="{{ $amount * 100 }}"> {{-- required in kobo --}}
                                    <input type="hidden" name="metadata" value="{{ json_encode($array = ['key_name' => 'value',]) }}" > {{-- For other necessary things you want to add to your payload. it is optional though --}}
                                    <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
                                    <input type="hidden" name="key" value="{{ config('paystack.secretKey') }}"> {{-- required --}}
                                    {{ csrf_field() }} {{-- works only when using laravel 5.1, 5.2 --}}

                                    <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- employ this in place of csrf_field only in laravel 5.0 --}}


                                    <p>
                                        <button class="btn btn-success btn-lg btn-block" type="submit" value="Pay Now!">
                                            <i class="fa fa-plus-circle fa-lg"></i> Pay Now!
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-1 hidden-sm hidden-xs"></div>
                    <h3 align="center">You can pay with the following :</h3>
                    <a href=""><div class="col-md-2 col-sm-3 col-xs-4" class="btn" data-toggle="tooltip" data-placement="bottom" title="Pay with Visa Card"><img src="{{ asset('agency_asset/dist/img/visa.jpg') }}" width="100%"></div></a>
                    <a href=""> <div class="col-md-2 col-sm-3 col-xs-4" class="btn " data-toggle="tooltip" data-placement="bottom" title="Pay with Master Card"><img src="{{ asset('agency_asset/dist/img/mastercard.jpg') }}" width="100%"></div></a>
                    <a href=""><div class="col-md-2 col-sm-3 col-xs-4"><img src="{{ asset('agency_asset/dist/img/verve.jpg') }}" width="100%" class="btn " data-toggle="tooltip" data-placement="bottom" title="Pay with Verve Card"></div></a>
                    <a href=""><div class="col-md-2 col-sm-3 col-xs-4" class="btn" data-toggle="tooltip" data-placement="bottom" title="Pay with Direct Debit"><img src="{{ asset('agency_asset/dist/img/directdebit.jpg') }}" width="100%"></div></a>


                    <a href="#">  <div class="col-md-2 col-sm-3 col-xs-4" class="btn" data-toggle="tooltip" data-placement="bottom" title="Pay with PayPal"><img src="{{ asset('agency_asset/dist/img/paypal.jpg') }}" width="100%"></div></a>
                    <div class="col-md-1 hidden-sm hidden-xs"></div>
                </div>

            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->

        </div>

    </section>

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
