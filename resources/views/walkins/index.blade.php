@extends('layouts.app')

@section('content')

@section('title', 'Faya | Dashboard')

<!-- Content Header (Page header) -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Walkins <small>All Walkins</small>

    </h1>
    <ol class="breadcrumb" style="font-size: 16px">

        <li><a href="#"><i class="fa fa-edit"></i> Walkins</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> All Walkins</a> </li>

    </ol>
</section>

<!-- Main content -->

<section class="content">
    <div class="row">
        <!-- /.col -->
        <div class="col-md-2 hidden-sm hidden-xs"></div>
        <!-- /.col -->
        <div class="row" style="padding: 5%">
            <div class="col-xs-12">
                <div class="col-md-11">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">
                                <!-- Post -->
                                <!-- /.post -->
                                <div class="box-body">
                                    <table class="table table-bordered table-striped campaign">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Campaign</th>
                                            <th>Delete</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="nc">

                            </div>
                            <!-- /.tab-pane -->

                            <div class="tab-pane" id="ne">

                            </div>

                            <div class="tab-pane" id="nw">

                            </div>

                            <div class="tab-pane" id="se">

                            </div>

                            <div class="tab-pane" id="ss">

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

            @foreach($walkins as $walkin)
            <div class="modal fade deleteModal{{ $walkin->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content" style="padding: 7%">
                        <h2 class="text-center">Are you sure you want to delete?</h2><br>
                        <h5><b style="color: red">Warning!!!</b> Deleting this means you might not be able to fully undo this oeration</h5>

                        <p align="center">
                            <button  class="btn btn-large btn-danger" data-dismiss="modal" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Cancel</button>
                            <a href="{{ route('walkins.delete', ['id' => $walkin->id]) }}" type="submit" class="btn btn-large btn-success" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Delete</a>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
        <!-- /.content -->
            @stop

            @section('scripts')
                <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
                <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
                {!! HTML::script('assets/js/moment.min.js') !!}
                {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}

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

                    var DataCampaign = $('.campaign').DataTable({
                        paging: true,
                        serverSide: true,
                        processing: true,
                        ajax: {
                            url: '/walkins/all-walkins/data',
                            data: function (d) {
                                d.start_date = $('input[name=txtFromDate_tvc]').val();
                                d.stop_date = $('input[name=txtToDate_tvc]').val();
                            }
                        },
                        columns: [
                            {data: 'id', name: 'id'},
                            {data: 'full_name', name: 'full_name'},
                            {data: 'email', name: 'email'},
                            {data: 'phone', name: 'phone'},
                            {data: 'campaign', name: 'campaign'},
                            {data: 'delete', name: 'name'}
                        ]
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

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop