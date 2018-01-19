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
                                    @if(count($walkin) === 0)
                                        <p class="text-center">No walkins, please create one</p>
                                    @else
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Campaign</th>
                                            <th>Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($walkin as $walkins)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $walkins->user->firstname }}
                                                </td>
                                                <td>
                                                    {{ $walkins->user->lastname }}
                                                </td>
                                                <td>
                                                    {{ $walkins->user->email }}
                                                </td>
                                                <td>
                                                    {{ $walkins->user->phone_number }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('campaign.create2', ['id' => 1, 'walkins' => $walkins->id]) }}" class="btn btn-primary btn-xs">Create Campaign</a>
                                                </td>
                                                <td>
                                                    <a href="#" data-toggle="modal" data-target=".bs-example1-modal-md{{ $walkins->id }}" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a></td>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                    @endif
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

            @foreach($walkin as $walkins)
            <div class="modal fade bs-example1-modal-md{{ $walkins->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content" style="padding: 7%">
                        <h2 class="text-center">Are you sure you want to delete?</h2><br>
                        <h5><b style="color: red">Warning!!!</b> Deleting this means you might not be able to fully undo this oeration</h5>

                        <p align="center">
                            <button  class="btn btn-large btn-danger" data-dismiss="modal" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Cancel</button>
                            <a href="{{ route('walkins.delete', ['id' => $walkins->id]) }}" type="submit" class="btn btn-large btn-success" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Delete</a>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach

        {{--</div>--}}
        <!-- /.content -->
            @stop

            @section('scripts')
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

            @section('style')
                <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
                <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop