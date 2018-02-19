@extends('layouts.new_app')
@section('title')
    <title>Advertiser | Create Campaigns</title>
@stop
@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Create Campaigns</h1>
                    <ul>
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Advertiser</a></li>
                        <li><a href="{{ route('advertiser.campaign.all') }}">All Campaign</a></li>
                    </ul>
                </div>
                <div class="Create-campaign">
                    <form class="campform" method="POST" action="{{ route('advertiser_campaign.store1', ['id' => $id]) }}">
                        {{ csrf_field() }}
                        <div class="col-12 ">
                            <h2>Campaign Details</h2>
                            <hr>
                            <p><br></p>
                            <p><br></p>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label class="col-md-2">Campaign Name:</label>
                                    <div class="col-md-6">
                                        <input type="text" name="name" class="form-control" value="{{ isset(((object) $step1)->name) ? ((object) $step1)->name: "" }}" required  placeholder="Campaign Name">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label class="col-md-2">Product:</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="product" value="{{ isset(((object) $step1)->product) ? ((object) $step1)->product : "" }}" required placeholder="Product">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label class="col-md-2">Brands:</label>
                                    <div class="col-md-4">
                                        <select name="brand" class="Role form-control">
                                            @foreach($brands as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label for="brand" class="col-md-2">Industry:</label>
                                    <div class="col-md-4">
                                        <select name="industry" class="Role form-control">
                                            @foreach($industry as $ind)
                                                <option value="{{ $ind->name }}">{{ $ind->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label for="targer_audience" class="col-md-2">Target Audience:</label>
                                    <div class="col-md-4">
                                        <select name="target_audience" class="Role form-control">
                                            @foreach($target as $target_audiences)
                                                <option value="{{ $target_audiences->id }}">{{ $target_audiences->audience }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label for="channel" class="col-md-2">Channel:</label>
                                    <div class="col-md-4">
                                        <select name="channel" class="Role form-control">
                                            @foreach($chanel as $chanels)
                                                <option value="{{ $chanels->id }}"
                                                        @if(isset(((object) $step1)->channel) === $chanels->id)
                                                        selected
                                                        @endif
                                                >{{ $chanels->channel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="start_date" class="col-md-2">Start Date:</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control flatpickr" value="{{ isset(((object) $step1)->start_date) ?((object) $step1)->start_date : "" }}" required name="start_date"  id="datepicker" placeholder="Start-Date">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="stop_date" class="col-md-2">Stop Date:</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control flatpickr" value="{{ isset(((object) $step1)->end_date) ? ((object) $step1)->end_date : "" }}" required name="end_date" id="datepicker1" placeholder="Stop-Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="min_age" class="col-md-2">Min Age:</label>
                                            <div class="col-md-6">
                                                <input type="number" name="min_age" required value="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="max_age" class="col-md-2">Max Age:</label>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control" name="max_age" required value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label for="day_parts" class="col-md-2">Day Parts:</label>
                                    <div class="col-md-8">
                                        @foreach($day_part as $day_parts)
                                            <input type="checkbox" name="dayparts[]" value="{{ $day_parts->id }}">{{ $day_parts->day_parts }}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label for="region" class="col-md-2">Region:</label>
                                    <div class="col-md-8">
                                        <input type="checkbox" name="region[]" value="{{ $region[0]->id }}">{{ $region[0]->region }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="input-group">
                                <input type="Submit" class="btn btn-danger btn-lg" name="Submit" value="Next Campaign">
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


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
    <script src="https://unpkg.com/flatpickr"></script>

    <script>
        flatpickr(".flatpickr", {
            altInput: true,
        });
    </script>
    3
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
@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
@stop



