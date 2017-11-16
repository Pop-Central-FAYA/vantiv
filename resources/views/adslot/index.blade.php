@extends('layouts.app')

@section('content')

@section('title', 'Faya | Dashboard')
    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Ad Management <small>Rate Card</small>

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-edit"></i> Ad Management</a> </li>
            <li><a href="index.html"><i class="fa fa-address-card"></i> Rate Card</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <div class="col-md-8 Campaign" style="padding:2%">
                <div class="row">
                    <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                    <div class="col-md-12" style="margin-top: -2%">
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
                    <div class="col-md-11">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" style="background:#eee">
                                <li class="active"><a href="#all" data-toggle="tab">All</a></li>
                                <li ><a href="#nc" data-toggle="tab">NC</a></li>
                                <li><a href="#ne" data-toggle="tab">NE</a></li>
                                <li><a href="#nw" data-toggle="tab">NW</a></li>
                                <li><a href="#se" data-toggle="tab">SE</a></li>
                                <li><a href="#ss" data-toggle="tab">SS</a></li>
                                <li><a href="#sw" data-toggle="tab">SW</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="active tab-pane" id="all">
                                    <!-- Post -->
                                    <!-- /.post -->
                                    <div class="box-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Day</th>
                                                <th>Time Slot</th>
                                                @foreach($seconds as $second)
                                                    <th>{{ $second->time_in_seconds }}</th>
                                                @endforeach
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($adslot as $adslots)
                                            <tr id="{{ $adslots->id }}">
                                                <td>Monday</td>

                                                <td>{{ $adslots->hourly_range[0]->time_range }}</td>
                                                @foreach($adslots->rate_card as $rating)
                                                    <td>{{ ((object)($rating))->price }}</td>
                                                @endforeach
                                                <td>
                                                    <a href="#" id="edit" edit_adslot_id = "{{ $adslots->id }}" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target=".bs-example1-modal-md" style="cursor: pointer;">  Edit</span></a>

                                                    {{--<a href="#" style="font-size: 16px"><span class="label label-danger">  <i class="fa fa-trash"></i></span></a></td>--}}
                                            </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
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

                <div class="modal fade bs-example1-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content" style="padding: 7%">
                            <h3 align="center">All / Monday / 09:00 - 10:00 am</h3>

                            <form class="selsec" style="margin-left:">
                                {{--@foreach($adslot as $adslots)--}}
                                    @foreach($adslots->rate_card as $rating)
                                        <p align="center"><input type="radio" name="duration" value="sec"> <span style="margin-left: 10px;"></span> <span class="sec-amount">{{ ((object)($rating))->price }}</span></p>
                                    @endforeach

                                {{--@endforeach--}}
                            </form>

                            <p align="center">
                                <a href="#" style="color:white"><button  class="btn btn-large btn-danger" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Cancel</button></a>
                                <a href="#" style="color:white"><button  class="btn btn-large btn-success" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Save</button></a>
                            </p>

                        </div>
                    </div>
                </div>


            {{--</div>--}}
<!-- /.content -->
@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}

    <script>
        $(document).ready(function() {
            $("body").delegate("#edit", "click", function() {
               var id = $(this).attr("edit_adslot_id");

            });
        })
    </script>
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