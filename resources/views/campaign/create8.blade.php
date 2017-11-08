@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
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
                                <img src="dist/img/nta-logo.jpg" width="100%">
                                <div class="tv-space">
                                    <p align="center">12 Available</p>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

                            <div class="tvspace-box">
                                <img src="dist/img/ait-logo.jpg" width="100%">
                                <div class="tv-space">
                                    <p align="center">12 Available</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="tvspace-box">
                                <img src="dist/img/silvebird-logo.jpg" width="100%">
                                <div class="tv-space">
                                    <p align="center">12 Available</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="tvspace-box">
                                <img src="dist/img/tvc-logo.jpg" width="100%">
                                <div class="tv-space">
                                    <p align="center">12 Available</p>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div id="tv-time-box" style="border:1px solid #ccc">
                        <div class="row">

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <img src="dist/img/nta-logo.jpg" width="65%">
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <h3 align="center">9:00 - 10:00 </h3>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <p align="center">9:00 - 9:03</p> <br/>
                                <span type="button"  data-toggle="modal" data-target=".bs-example-modal-lg" class="avail-box" style="background:#ffde01; cursor: pointer; "></span>



                                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <h2 align="center">90 Seconds Available</h2>

                                            <ul style="font-size: 21px; margin:0 auto; width: 50%">
                                                <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy.mpeg     <span style="margin-left:15%">60 seconds</span> </li>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy1.mpeg   <span style="margin-left:15%">30 second</span> </li>

                                            </ul>
                                            <p align="center">
                                                <a href="#" style="color:white"><button  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ">
                                <p align="center">9:15 - 9:18</p> <br/>
                                <span type="button"  data-toggle="modal" data-target=".bs-example2-modal-lg" class="avail-box" style="cursor: pointer; "></span>


                                <div class="modal fade bs-example2-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <h2 align="center">90 Seconds Available</h2>

                                            <ul style="font-size: 21px; margin:0 auto; width: 50%">
                                                <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy.mpeg     <span style="margin-left:15%">60 seconds</span> </li>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy1.mpeg   <span style="margin-left:15%">30 second</span> </li>

                                            </ul>
                                            <p align="center">
                                                <a href="#" style="color:white"><button  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 choosen">
                                <p align="center">9:30 - 9:33</p> <br/>
                                <span type="button"  data-toggle="modal" data-target=".bs-example3-modal-lg" class="avail-box" style="cursor: pointer; "></span>


                                <div class="modal fade bs-example3-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <h2 align="center">90 Seconds Available</h2>

                                            <ul style="font-size: 21px; margin:0 auto; width: 50%">
                                                <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy.mpeg     <span style="margin-left:15%">60 seconds</span> </li>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy1.mpeg   <span style="margin-left:15%">30 second</span> </li>

                                            </ul>
                                            <p align="center">
                                                <a href="#" style="color:white"><button  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <p align="center">9:45 - 9:48</p> <br/>


                                <span type="button"  data-toggle="modal" data-target=".bs-example4-modal-lg" class="avail-box" style="background:#f4290d; cursor: pointer; "></span>


                                <div class="modal fade bs-example4-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <h2 align="center">Unavailable</h2>


                                            <p align="center">
                                                <a href="#" style="color:white"><button  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;" disabled="">Save</button></a></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <img src="dist/img/nta-logo.jpg" width="65%">
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <h3 align="center">10:00 - 11:00 </h3>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <p align="center">10:00 - 9:03</p> <br/>
                                <span type="button"  data-toggle="modal" data-target=".bs-example5-modal-lg" class="avail-box" style="background:#ffde01; cursor: pointer; "></span>



                                <div class="modal fade bs-example5-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <h2 align="center">90 Seconds Available</h2>

                                            <ul style="font-size: 21px; margin:0 auto; width: 50%">
                                                <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy.mpeg     <span style="margin-left:15%">60 seconds</span> </li>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy1.mpeg   <span style="margin-left:15%">30 second</span> </li>

                                            </ul>
                                            <p align="center">
                                                <a href="#" style="color:white"><button  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                        </div>
                                    </div>
                                </div>



                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 choosen">
                                <p align="center">10:15 - 10:18</p> <br/>
                                <span type="button"  data-toggle="modal" data-target=".bs-example6-modal-lg" class="avail-box" style="cursor: pointer; "></span>


                                <div class="modal fade bs-example6-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <h2 align="center">90 Seconds Available</h2>

                                            <ul style="font-size: 21px; margin:0 auto; width: 50%">
                                                <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy.mpeg     <span style="margin-left:15%">60 seconds</span> </li>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy1.mpeg   <span style="margin-left:15%">30 second</span> </li>

                                            </ul>
                                            <p align="center">
                                                <a href="#" style="color:white"><button  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <p align="center">10:30 - 10:33</p> <br/>
                                <span type="button"  data-toggle="modal" data-target=".bs-example7-modal-lg" class="avail-box" style="cursor: pointer; "></span>


                                <div class="modal fade bs-example7-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <h2 align="center">90 Seconds Available</h2>

                                            <ul style="font-size: 21px; margin:0 auto; width: 50%">
                                                <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy.mpeg     <span style="margin-left:15%">60 seconds</span> </li>
                                                <hr />
                                                <li><i class="fa fa-video-camera"></i> Fazzy1.mpeg   <span style="margin-left:15%">30 second</span> </li>

                                            </ul>
                                            <p align="center">
                                                <a href="#" style="color:white"><button  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <p align="center">10:45 - 10:48</p> <br/>
                                <span type="button"  data-toggle="modal" data-target=".bs-example8-modal-lg" class="avail-box" style="background:#f4290d; cursor: pointer; "></span>


                                <div class="modal fade bs-example8-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content" style="padding: 5%">
                                            <h2 align="center">Unavailable</h2>


                                            <p align="center">
                                                <a href="#" style="color:white"><button  class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;" disabled="">Save</button></a></p>
                                        </div>
                                    </div>
                                </div>
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
                <a href="create-campaign-page7.html"><button class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button></a>
                <a href="create-campaign-page9.html"><button class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button></a>

            </p>
        </div>
    </section>

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



