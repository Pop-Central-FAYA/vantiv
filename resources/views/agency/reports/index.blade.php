@extends('agency_layouts.app')
@section('title')
    <title>Agency | Report</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Agency Report

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="{{ route('dashboard') }}"><i class="fa fa-th"></i> Agency</a> </li>
            <li><a href="#"><i class="fa fa-address-card"></i> Report</a> </li>

        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <div class="col-md-8 Campaign" style="padding:2%"></div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->

            <div class="row" style="padding: 5%">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div id="load_broad" class="load_broad" style="display: none;"></div>
                            <form action="" method="GET" id="client_filter">
                                <label for="clients">Clients</label>
                                <select name="client" id="client" class="form-control">
                                    <option value="">Please Select an Agency</option>
                                    @foreach($user as $users)
                                        <option value="{{ $users['user_id'] }}">{{ $users['name'] }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    <p><br></p>
                    <div class="col-md-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" style="background:#eee">
                                <li class="active"><a href="#campaigns" data-toggle="tab">Campaigns</a></li>
                                <li><a href="#revenue" data-toggle="tab">Revenue</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="campaigns">
                                    <div class="row">
                                        <div class="row">
                                            <form action="#" method="GET">
                                                {{ csrf_field() }}
                                                <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                                                <div class="col-md-10" style="margin-top: -2%">
                                                    <div class="input-group date styledate">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" placeholder="start-date" value="" required name="txtFromDate_camp" class="form-control txtFromDate" id="txtFromDate" />
                                                    </div>

                                                    <div class="input-group date styledate">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" placeholder="stop-date" value="" required name="txtToDate_camp" class="form-control txtToDate" id="txtToDate" />
                                                    </div>

                                                    <div class="input-group" style="">
                                                        <input type="button" class="search-btn" id="button_campaign" value="Apply" style="float:left" >
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <hr>
                                        <div class="col-xs-12">
                                            <div class="box"    >
                                                <div class="box-body c_r">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered campaign_report">
                                                            <thead>
                                                                <th>S/N</th>
                                                                <th>Campaign Name</th>
                                                                <th>Start</th>
                                                                <th>Stop</th>
                                                                <th>Amount(&#8358;)</th>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="revenue">
                                    <div class="row">
                                        <div class="row">
                                            <form action="#" method="GET">
                                                {{ csrf_field() }}
                                                <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                                                <div class="col-md-10" style="margin-top: -2%">
                                                    <div class="input-group date styledate">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" placeholder="start-date" value="" required name="txtFromDate_rev" class="form-control txtFromDate" id="txtFromDate" />
                                                    </div>

                                                    <div class="input-group date styledate">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" placeholder="stop-date" value="" required name="txtToDate_rev" class="form-control txtToDate" id="txtToDate" />
                                                    </div>

                                                    <div class="input-group" style="">
                                                        <input type="button" class="search-btn" id="button_rev" value="Apply" style="float:left" >
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <hr>
                                        <div class="col-xs-12">
                                            <div class="box">
                                                <div class="box-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered revenue_report">
                                                            <thead>
                                                            <th>S/N</th>
                                                            <th>Date</th>
                                                            <th>Campaign Name</th>
                                                            <th>Total Amount/Revenue (&#8358;)</th>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.col -->

                </div>
            </div>
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

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>


    <script>
        $(document).ready(function(){
            $(".txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $(".txtToDate").datepicker({
                numberOfMonths: 2,
                onSelect: function(selected) {
                    $("#txtFromDate").datepicker("option","maxDate", selected)
                }
            });

            var DatefilterCampaign =  $('.campaign_report').DataTable({
                paging: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/agency/reports/campaign/all-data',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_camp]').val();
                        d.stop_date = $('input[name=txtToDate_camp]').val();
                        d.client = $('select#client').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'campaign_name', name: 'campaign_name'},
                    {data: 'start', name: 'start'},
                    {data: 'stop', name: 'stop'},
                    {data: 'amount', name: 'amount'},
                ]
            });

            var DatefilterRevenue =  $('.revenue_report').DataTable({
                paging: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/agency/reports/revenue/all-data',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_rev]').val();
                        d.stop_date = $('input[name=txtToDate_rev]').val();
                        d.client = $('select#client').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'date', name: 'date'},
                    {data: 'campaign_name', name: 'campaign_name'},
                    {data: 'amount', name: 'amount'},
                ]
            });


            $('#client').change( function() {
                DatefilterCampaign.draw();
                DatefilterRevenue.draw();
            });

            $('#button_campaign').click(function () {
                DatefilterCampaign.draw();
            })

            $('#button_rev').click(function () {
                DatefilterRevenue.draw();
            })

        });
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

@section('style')
    <style>
        .load_broad {
            position: fixed;
            top: 50%;
            left: 50%;
            margin-left: -50px; /* half width of the spinner gif */
            margin-top: -50px; /* half height of the spinner gif */
            text-align:center;
            z-index:1234;
            overflow: auto;
            width: 500px; /* width of the spinner gif */
            height: 500px; /*hight of the spinner gif +2px to fix IE8 issue */
        }
    </style>
@stop

