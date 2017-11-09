@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Create Campaign
            <small><i class="fa fa-users"></i> Audience</small>
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Create Campaign</a> </li>
            <li><i class="fa fa-users"></i> Audience </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-1 hidden-sm hidden-xs"></div>
            <div class="col-md-9 " style="padding:2%">
                <form class="campform">
                    <div class="row">
                        <div class="col-md-6">

                            <label>Target Audience</label>
                            <select>
                                <option>Business People</option>
                                <option>Civil Servant</option>
                                <option>Urban People</option>
                                <option>Rural People</option>

                            </select>

                        </div>

                        <div class="col-lg-6 col-md-6 hidden-sm hidden-xs"></div>
                    </div>
                    <div class="row" style="margin-top:3%">

                        <div class="col-md-2">

                            <label style="margin-left:10%">Min Age:</label>
                            <select style="width: 100%">
                                <option>17</option>
                                <option>26</option>
                                <option>30</option>
                                <option>40</option>
                                <option>50</option>

                            </select>
                        </div>

                        <div class="col-md-2">

                            <label style="margin-left:10%">Max Age:</label>
                            <select style="width: 100%">
                                <option>21</option>
                                <option>29</option>
                                <option>39</option>
                                <option>49</option>
                                <option>70</option>

                            </select>
                        </div>



                    </div>



                    <div class="row" style="margin-top:10%">
                        <h3> Region </h3>
                        <div class="col-md-2">
                            <div class="form-group">

                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        NC
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        NE
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        NW
                                    </label>
                                </p>

                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        SE
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        SS
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        Overnight
                                    </label>
                                </p>

                            </div>
                        </div>
                    </div>

                </form>

            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->




        </div>
        <!-- /.row -->

        <div class="container">

            <p align="right">
                <a href="create-campaign-page2.html"><button class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button></a>
                <a href="create-campaign-page4.html"><button class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button></a>

            </p>
    </section>

@stop

@section('scripts')
    <script src="{{ asset('asset/plugins/select2/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('asset/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <!-- bootstrap time picker -->
    <script src="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>


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

@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('asset/plugins/iCheck/all.css') }}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
    <!-- Bootstrap time Picker -->
    <link rel="stylesheet" href="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('asset/plugins/select2/select2.min.css') }}">
    @stop