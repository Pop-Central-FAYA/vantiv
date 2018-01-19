@extends('agency_layouts.app')
@section('title')
    <title>Agency | Dashboard</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Agency Dashboard

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Agency</a> </li>
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-address-card"></i> Dashboard</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <h3>Company</h3>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <!-- AREA CHART -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Area Chart</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="chart">
                            <canvas id="barChart" style="height:250px"></canvas>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                <!-- DONUT CHART -->

                <!-- /.box -->

            </div>
            <!-- /.col (LEFT) -->
            <div class="col-md-6">
                <!-- LINE CHART -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Line Chart</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="chart">
                            <canvas id="pieChart" style="height:250px"></canvas>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                <!-- BAR CHART -->
                <div class="box box-success" style="display: none">

                    <div class="box-body">
                        <div class="chart">
                            <canvas id="areaChart" style="height:230px"></canvas>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <!-- /.col (RIGHT) -->
        </div>
        <!-- /.row -->

        <div class="row">

            <div class="client-box">


                <div class="col-md-2">
                    <div class="cb-box">
                        <a href="#"><span data-toggle="modal" data-target=".bs-example1-modal-lg" style="cursor: pointer; ">
                       <p align="center">
                          <img src="{{ asset('agency_asset/dist/img/add-icon.png') }}" width="70%"> <br />
                          <h4 align="center">Add Client</h4>
                                </p></span>
                        </a>
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="#">
                        <div class="cb-box">
                            <p align="center">
                                <img src="{{ asset('agency_asset/dist/img/flickr.png') }}" width="70%"> <br />
                            <h4 align="center">Flickr</h4>
                            </p>
                        </div>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="#">
                        <div class="cb-box">
                            <p align="center">
                                <img src="{{ asset('agency_asset/dist/img/coke.png') }}" width="70%"> <br />
                            <h4 align="center">Coca Cola</h4>
                            </p>
                        </div>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="#">
                        <div class="cb-box">
                            <p align="center">
                                <img src="{{ asset('agency_asset/dist/img/nike.png') }}" width="70%"> <br />
                            <h4 align="center">Nike</h4>
                            </p>
                        </div>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="#">
                        <div class="cb-box">
                            <p align="center">
                                <img src="{{ asset('agency_asset/dist/img/pepsi.png') }}" width="70%"> <br />
                            <h4 align="center">Pespi</h4>
                            </p>
                        </div>
                    </a>
                </div>

                <div class="col-md-2">
                    <a href="#">
                        <div class="cb-box">
                            <p align="center">
                                <img src="{{ asset('agency_asset/dist/img/apple.png') }}" width="70%"> <br />
                            <h4 align="center">Apple</h4>
                            </p>
                        </div>
                    </a>
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

    <script>
        $(document).ready(function () {
            var Datefilter =  $('.agency_campaign_all').DataTable({
                paging: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/agency/campaigns/all-campaign/data',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_hvc]').val();
                        d.stop_date = $('input[name=txtToDate_hvc]').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'brand', name: 'brand'},
                    {data: 'product', name: 'product'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'amount', name: 'amount'},
                    {data: 'mpo', name: 'mpo'}
                ]
            });
        })
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

