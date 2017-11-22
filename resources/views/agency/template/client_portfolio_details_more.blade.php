@extends('agency_layouts.app')
@section('title')
    <title>Agency | Campaign-Portfolio Details</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Client Portfolio - Google

        </h1>
        <hr/>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Agency</a> </li>
            <li><a href="client-portfolio-details.html"><i class="fa fa-address-card"></i> Client Portfolio details</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">

        <div class="row">

            <div class="col-xs-12">




                <div class="col-md-6" style="margin-bottom: 10px;"></div>


                <div class="col-md-12">
                    <h3 align="right">Current Balance: N200,000</h3>
                    <h2>Google</h2>

                    <table class="table table-bordered table-striped" style="font-size: 16px">
                        <thead>
                        <tr>
                            <th>P.Name</th>
                            <th>No. of Campaigns</th>
                            <th>Total Expense</th>
                            <th>Date Created</th>
                            <th>Last Campaign</th>

                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td>
                                <a href="#">
                                    <p>
                                        <img src="{{ asset('agency_asset/dist/img/googleplus.png') }}" width="20%">
                                    </p>
                                </a>

                            </td>
                            <td>14</td>
                            <td>1,900,000</td>
                            <td>27 September, 2015</td>
                            <td>25 March, 2017</td>

                        </tr>
                        </tbody>
                    </table>

                    <div class="col-xs-12">
                        <div class="box">

                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-striped" style="font-size: 16px">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Budget</th>
                                        <th>Amount</th>
                                        <th>Media PI</th>
                                        <th>C. Status</th>
                                        <th>MPO</th>
                                        <th>Invoice</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>

                                        <td>001</td>
                                        <td>Search</td>
                                        <td>02-03-2017</td>
                                        <td>25,000</td>
                                        <td>12,000</td>
                                        <td><a href="#"  data-toggle="modal" data-target=".bs-example1-modal-lg">Pending</a></td>
                                        <td>Complete</td>
                                        <td><a href="#">View</a></td>
                                        <td><a href="#" data-toggle="modal" data-target=".bs-example2-modal-lg">View</a></td>
                                    </tr>

                                    <tr>

                                        <td>002</td>
                                        <td>Search</td>
                                        <td>02-03-2017</td>
                                        <td>25,000</td>
                                        <td>12,000</td>
                                        <td><a href="#" data-toggle="modal" data-target=".bs-example1-modal-lg">Approve</a></td>
                                        <td>Complete</td>
                                        <td><a href="#">View</a></td>
                                        <td><a href="#" data-toggle="modal" data-target=".bs-example2-modal-lg">View</a></td>
                                    </tr>

                                    <tr>

                                        <td>003</td>
                                        <td>Search</td>
                                        <td>02-03-2017</td>
                                        <td>25,000</td>
                                        <td>12,000</td>
                                        <td><a href="#" data-toggle="modal" data-target=".bs-example1-modal-lg">Pending</a></td>
                                        <td>Complete</td>
                                        <td><a href="#">View</a></td>
                                        <td><a href="#" data-toggle="modal" data-target=".bs-example2-modal-lg">View</a></td>
                                    </tr>
                                    <tr>

                                        <td>004</td>
                                        <td>Search</td>
                                        <td>02-03-2017</td>
                                        <td>25,000</td>
                                        <td>12,000</td>
                                        <td><a href="#" data-toggle="modal" data-target=".bs-example1-modal-lg">Review</a></td>
                                        <td>Complete</td>
                                        <td><a href="#">View</a></td>
                                        <td><a href="#" data-toggle="modal" data-target=".bs-example2-modal-lg">View</a></td>
                                    </tr>


                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>

                </div>
                <!-- /.col -->

            </div>




            <!-- /.row -->




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
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
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
@stop