@extends('agency_layouts.app')
@section('title')
    <title>Agency | Campaign-Lists</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            All Campaign

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Agency</a> </li>
            <li><a href="index.html"><i class="fa fa-address-card"></i> All Campaign</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <div class="col-md-8 Campaign" style="padding:2%">

                <div class="row">
               <span><label>Brand</label>
         <select class="add-disc" style="width: 30%; border:none; padding: 1%; float:none">
          <option>Business</option>
                    <option>Business</option>
        </select>
          </span>
                </div>

                <div class="row">

                    <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                    <div class="col-md-10" style="margin-top: -2%">
                        <div class="input-group date styledate" style="width:30% !important">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" placeholder="Start Date" class="form-control pull-right" id="datepicker" >
                        </div>

                        <div class="input-group date styledate" style="width:30% !important">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" placeholder="End Date" class="form-control pull-right" id="datepickerend" >
                        </div>
                        <div class="input-group" style="">
                            <input type="submit" class="search-btn" value="search" style="float:left" >
                        </div>
                    </div>
                </div>





            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->

            <div class="row" style="padding: 5%">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Hover Data Table</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Submitted</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>MPO</th>
                                    <th>Invoice</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>001</td>
                                    <td>Building</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02.03.2017</td>
                                    <td>20,500</td>
                                    <td>Approve </td>
                                    <td><a href="#"style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target=".bs-example1-modal-lg" style="cursor: pointer; "> Views </span></a></td>

                                    <td><a href="#"style="font-size: 16px"><span class="label label-danger" data-toggle="modal" data-target=".bs-example1-modal-lg" style="cursor: pointer; "> Views </span></a></td>
                                </tr>
                                <tr>
                                    <td>002</td>
                                    <td>Tech</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02.03.2017</td>
                                    <td>30,500</td>
                                    <td>Approve </td>
                                    <td><a href="#"style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target=".bs-example1-modal-lg" style="cursor: pointer; "> Views </span></a></td>

                                    <td><a href="#"style="font-size: 16px"><span class="label label-danger" data-toggle="modal" data-target=".bs-example1-modal-lg" style="cursor: pointer; "> Views </span></a></td>
                                </tr>


                                <tr>
                                    <td>003</td>
                                    <td>Grow</td>
                                    <td>MNO</td>
                                    <td>Wizzy</td>
                                    <td>02.03.2017</td>
                                    <td>31,500</td>
                                    <td>Approve </td>
                                    <td><a href="#"style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target=".bs-example1-modal-lg" style="cursor: pointer; "> Views </span></a></td>

                                    <td><a href="#"style="font-size: 16px"><span class="label label-danger" data-toggle="modal" data-target=".bs-example1-modal-lg" style="cursor: pointer; "> Views </span></a></td>
                                </tr>


                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>


                <div class="modal fade bs-example1-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <div class="modal-content" style="padding: 5%">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h2 class="modal-title" id="myModalLabel">MPO - Fun</h2>
                            </div>

                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>

                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Ad block</th>
                                        <th>Duration</th>
                                        <th>Media</th>
                                        <th>Price</th>
                                        <th>Approval</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>

                                        <td>Fun</td>
                                        <td>Fizzy</td>
                                        <td>09:20 - 09:24</td>
                                        <td>02.03.2017</td>
                                        <td>15 Secs</td>
                                        <td>view </td>
                                        <td>11,000 </td>
                                        <td style="color:green"><i class="fa fa-check-circle" aria-hidden="true"></i> Approve </td>

                                    </tr>
                                    <tr>

                                        <td></td>
                                        <td>Fizzy</td>
                                        <td>09:20 - 09:24</td>
                                        <td>02.03.2017</td>
                                        <td>15 Secs</td>
                                        <td>view </td>
                                        <td>11,000 </td>
                                        <td style="color:green"><i class="fa fa-check-circle" aria-hidden="true"></i> Approve </td>

                                    </tr>


                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>

                                <h4>Discount: 10%</h4>
                                <h2>Total: N33,000</h2>
                            </div>




                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Done</button>
                            </div>
                        </div>
                    </div>
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