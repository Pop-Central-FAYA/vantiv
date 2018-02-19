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
                    <form class="campform" method="POST" action="{{ route('advertiser_campaign.store3_3', ['id' => $id, 'broadcaster' => $broadcaster]) }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="col-12 ">
                            <h2>Choose Media: Step 4</h2>
                            <hr>
                            <p><br></p>
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12 ">

                                    <p align="center">The history of advertising can be traced to ancient civilizations. It became a major force in capitalist economies in the mid-19th century, based primarily on newspapers and magazines. In the 20th century, advertising grew rapidly with new technologies such as direct mail, radio, television, the internet and mobile devices.</p>

                                </div>
                            </div>
                            {{--{{ dd($time_in_sec) }}--}}
                            <div class="row" style="margin-top:3%" id="dynamic">
                                <div class="row b">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Upload Media</label>
                                            <input type="file" class="form-control" id="fup" name="uploads">
                                            <input type="hidden" class="form-control" name="f_du" id="f_du" size="5" />
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Duration </label> <br />
                                            <select style="width: 60%" name="time" class="form-control">
                                                <option value="60">60 Seconds</option>
                                            </select>
                                            {{--<button type="button" id="add_more" class="btn btn-info btn-xs add_more">+ Add More</button>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">

                            </div>

                            <audio id="audio"></audio>

                            <div class="container">

                                <p align="right">
                                    {{--<button id="step3" type="button" class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>--}}
                                    <button type="submit" class="btn campaign-button btn-danger btn-lg" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button>
                                </p>
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

    <script>
        // Code to get duration of audio /video file before upload - from: http://coursesweb.net/

        //register canplaythrough event to #audio element to can get duration
        var f_duration =0;  //store duration
        document.getElementById('audio').addEventListener('canplaythrough', function(e){
            //add duration in the input field #f_du
            f_duration = Math.round(e.currentTarget.duration);
            document.getElementById('f_du').value = f_duration;
            URL.revokeObjectURL(obUrl);
        });

        //when select a file, create an ObjectURL with the file and add it in the #audio element
        var obUrl;
        document.getElementById('fup').addEventListener('change', function(e){
            var file = e.currentTarget.files[0];
            //check file extension for audio/video type
            if(file.name.match(/\.(avi|mp3|mp4|mpeg|ogg)$/i)){
                obUrl = URL.createObjectURL(file);
                document.getElementById('audio').setAttribute('src', obUrl);
            }
        });
    </script>
@stop

