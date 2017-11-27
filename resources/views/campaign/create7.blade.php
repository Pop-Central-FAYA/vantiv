@extends('layouts.app')

@section('content')

    <section class="content-header">
        <h1>
            Create Campaign
            <small><i class="fa fa-file-video-o"></i> Adslot </small>
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Create Campaign</a> </li>
            <li><i class="fa fa-file-video-o"></i> Adslot </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-1 hidden-sm hidden-xs"></div>
            <div class="col-md-9 " style="padding:2%">
                <form class="campform">
                    <div class="row">
                        <div class="col-md-12 ">
                            <h2></h2>
                            <p align="center">The history of advertising can be traced to ancient civilizations. It became a major force in capitalist economies in the mid-19th century, based primarily on newspapers and magazines. In the 20th century, advertising grew rapidly with new technologies such as direct mail, radio, television, the internet and mobile devices.</p>

                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 5%">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 ">
                            <div class="tvspace-box">
                                <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="100%">
                                <div class="tv-space">
                                    <p align="center">{{ $counting }} Available</p>
                                    <p>{{ Session::get('broadcaster_brand') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tv-time-box" style="border:1px solid #ccc">
                        @foreach($adslot as $adslots)
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                    <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="65%">
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                    <h3 align="center">{{ $adslots->hourly_range->time_range }}</h3>
                                </div>
                                @foreach($adslots->rate_card as $rating)
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <p align="center">{{ ((object)($rating))->time_in_seconds_id->time_in_seconds }} Seconds</p> <br/>
                                <span type="button"  data-toggle="modal" data-target=".bs-example-modal-lg{{ ((object)($rating))->id }}" class="avail-box" style="background:green; cursor: pointer; "></span>
                                        <div class="modal fade bs-example-modal-lg{{ ((object)($rating))->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <form action="" method="POST">
                                                <h2 align="center">{{ ((object)($rating))->time_in_seconds_id->time_in_seconds }} Seconds Available</h2>
                                                <ul style="font-size: 21px; margin:0 auto; width: 80%">
                                                    <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                    <hr />
                                                    @foreach($data as $datas)
                                                        @if($datas['time'] === ((object)($rating))->time_in_seconds_id->id)
                                                            <div class="row">
                                                                <div class="col-md-6"><i class="fa fa-video-camera"></i> {{ $datas['file'] }}</div>
                                                                <input type="hidden" name="file[]" value="{{ $datas['file'] }}">
                                                                <div class="col-md-3"><span style="margin-left:15%"></span>{{ ((object)($rating))->time_in_seconds_id->time_in_seconds }} Seconds</div>
                                                                <input type="hidden" name="time_id[]" value="{{ ((object)($rating))->time_in_seconds_id->id }}">
                                                                <div class="col-md-3"><input name="hourly_range[]" type="checkbox"></div>
                                                            </div>
                                                            <hr />
                                                        @endif
                                                    @endforeach
                                                </ul>
                                                <button type="submit"  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                @endforeach
                            </div>
                        @endforeach
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
                <button id="step6" class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>
                <a href="create-campaign-page8.html"><button class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button></a>

            </p>
        </div>
    </section>

    <!-- /.content -->

@endsection

@section('scripts')

    <script src="{{ asset('asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>

    <!-- bootstrap datepicker -->
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>

    <!-- date-range-picker -->
    <script src="{{ 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js' }}"></script>
    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('asset/plugins/iCheck/icheck.min.js') }}"></script>

    <!-- bootstrap color picker -->
    <script src="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

    <!-- bootstrap time picker -->
    <script src="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#step6').click(function(){
                window.location.href = "/campaign/create/1/step";
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

@endsection
