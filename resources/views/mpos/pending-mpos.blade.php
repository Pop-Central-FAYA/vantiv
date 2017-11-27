@extends('layouts.app')

@section('title', trans('app.pending-mpos'))

@section('stylesheets')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />

@endsection


@section('content')

    <section class="content-header">
        <h1>
            Pending Media Purchase orders

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> MPOs</a> </li>
            <li><a href="index.html"><i class="fa fa-address-card"></i> Pending MPOs</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <div class="col-md-8 Campaign" style="padding:2%">



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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>

                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Fun F</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02/03/2017</td>
                                    <td>07/03/2017</td>


                                    <td><a href="mpo-page2.html" style="font-size: 16px"><span class="label label-danger">  view media</span></a></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Fun F</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02/03/2017</td>
                                    <td>07/03/2017</td>


                                    <td><a href="mpo-page2.html" style="font-size: 16px"><span class="label label-danger">  view media</span></a></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Fun F</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02/03/2017</td>
                                    <td>07/03/2017</td>


                                    <td><a href="mpo-page2.html" style="font-size: 16px"><span class="label label-danger">  view media</span></a></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Fun F</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02/03/2017</td>
                                    <td>07/03/2017</td>


                                    <td><a href="mpo-page2.html" style="font-size: 16px"><span class="label label-danger">  view media</span></a></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Fun F</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02/03/2017</td>
                                    <td>07/03/2017</td>


                                    <td><a href="mpo-page2.html" style="font-size: 16px"><span class="label label-danger">  view media</span></a></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Fun F</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02/03/2017</td>
                                    <td>07/03/2017</td>


                                    <td><a href="mpo-page2.html" style="font-size: 16px"><span class="label label-danger">  view media</span></a></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Fun F</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02/03/2017</td>
                                    <td>07/03/2017</td>


                                    <td><a href="mpo-page2.html" style="font-size: 16px"><span class="label label-danger">  view media</span></a></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>Fun F</td>
                                    <td>ABC</td>
                                    <td>Fizzy</td>
                                    <td>02/03/2017</td>
                                    <td>07/03/2017</td>


                                    <td><a href="mpo-page2.html" style="font-size: 16px"><span class="label label-danger">  view media</span></a></td>
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
        </div>



    </section>

@stop


@section('scripts')

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

        });
    </script>

@stop