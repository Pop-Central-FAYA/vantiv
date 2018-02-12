@extends('advertiser_layouts.app')
@section('title')
    <title>Agency | Create Campaigns</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Create Campaigns
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Agency</a> </li>
            <li><a href="index.html"><i class="fa fa-address-card"></i> Create Advertiser Campaign</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <section class="content">
            <div class="row">
                <div class="col-md-1 hidden-sm hidden-xs"></div>
                <div class="col-md-9 " style="padding:2%">
                    <form class="campform" method="POST" action="{{ route('advertiser_campaign.store1', ['id' => $id]) }}">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="name" placeholder="Campaign Name" value="{{ isset(((object) $step1)->name) ? ((object) $step1)->name: "" }}" required placeholder="Name">
                                <input type="hidden" name="user_id" value="">
                            </div>

                            <div class="col-lg-6 col-md-6 hidden-sm hidden-xs"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label style="margin-left:10%">Brands:</label>
                                <select name="brand" class="form-control" id="">
                                    @foreach($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br>
                            <div class="col-md-6">
                                <input type="text" name="product" value="{{ isset(((object) $step1)->product) ? ((object) $step1)->product : "" }}" required placeholder="Product">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label style="margin-left:10%">Industry:</label>
                                <select name="industry" id="" class="form-control">
                                    @foreach($industry as $ind)
                                        <option value="{{ $ind->name }}">{{ $ind->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label style="margin-left:10%">Target Audience:</label>
                                <select name="target_audience" id="" class="form-control">
                                    @foreach($target as $target_audiences)
                                        <option value="{{ $target_audiences->id }}">{{ $target_audiences->audience }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <dvi class="col-md-3">
                                <label style="margin-left:10%">Channel:</label>
                                <select style="width: 100%" class="form-control" name="channel">
                                    @foreach($chanel as $chanels)
                                        <option value="{{ $chanels->id }}"
                                                @if(isset(((object) $step1)->channel) === $chanels->id)
                                                selected
                                                @endif
                                        >{{ $chanels->channel }}</option>
                                    @endforeach
                                </select>
                            </dvi>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group date styledate">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" readonly placeholder="start-date" value="{{ isset(((object) $step1)->start_date) ?((object) $step1)->start_date : "" }}" required name="start_date" class="form-control" id="txtFromDate" />
                                </div>

                                <div class="input-group date styledate">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" readonly placeholder="stop-date" value="{{ isset(((object) $step1)->end_date) ? ((object) $step1)->end_date : "" }}" required name="end_date" class="form-control" id="txtToDate" />
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label style="margin-left:10%">Min Age:</label>
                                <input type="number" name="min_age" required class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label style="margin-left:10%">Max Age:</label>
                                <input type="number" name="max_age" required class="form-control">
                            </div>
                        </div>
                        <br>
                        <div class="col-md-6">
                            <h3> Day Parts </h3>
                            <div class="form-group">
                                @foreach($day_part as $day_parts)
                                    <p>
                                        <label>
                                            {{ $day_parts->day_parts }}
                                            <input type="checkbox" name="dayparts[]" value="{{ $day_parts->id }}"/>
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3> Region </h3>
                            <div class="form-group">
                                    <p>
                                        <label>
                                            {{ $region[0]->region }}
                                            <input type="checkbox" name="region[]" value="{{ $region[0]->id }}">
                                        </label>
                                    </p>
                            </div>
                        </div>

                        <div class="container">
                            <div class="container">
                                <p align="right">
                                    <button class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button>
                                </p>
                            </div>
                    </form>
                </div>
                <!-- /.col -->
                <div class="col-md-2 hidden-sm hidden-xs"></div>
                <!-- /.col -->
            </div>
            <!-- /.row -->


        </section>
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


        $(document).ready(function(){
            $("#txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $("#txtToDate").datepicker({
                numberOfMonths: 2,
                onSelect: function(selected) {
                    $("#txtFromDate").datepicker("option","maxDate", selected)
                }
            });

        });
    </script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();

            //Datemask dd/mm/yyyy
            $("#datemask").inputmask("yyyy-mm-dd", {"placeholder": "dd/mm/yyyy"});
            //Datemask2 mm/dd/yyyy
            $("#datemask2").inputmask("yyyy-mm-dd", {"placeholder": "mm/dd/yyyy"});
            //Money Euro
            $("[data-mask]").inputmask();

            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD h:mm A'});
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
                    $('#daterange-btn span').html(start.format('YYYY MMMM, D') + ' - ' + end.format('YYYY MM, D'));
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


