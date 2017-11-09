@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Create Campaign
            <small><i class="fa fa-file"></i> Payment </small>
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Create Campaign</a> </li>
            <li><i class="fa fa-file"></i> Summary </li>

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

                            <h2>Summary</h2>
                            <hr / style="border-bottom: 1px solid #333">

                            <div class="col-md-6">
                                <p><b>Campaign Name:</b> TV Ad  </p>
                                <p><b>Brand Name:</b> Coca Cola  </p>
                                <p><b>Product Name:</b> Eva Water  </p>
                                <p><b>Date:</b> 20/11/2017  </p>
                            </div>
                            <div class="col-md-6">

                                <p><b><i class="fa fa-date"></i> Date Part: </b> Prime Time (7:00 PM - 7:03 PM) </p>
                                <p> <b><i class="fa fa-users"></i> Audience: </b> Business People</p>
                                <p><b><i class="fa fa-user"></i> Viewers age:  </b> 20 - 29yrs </p>
                                <p><b><i class="fa fa-map-marker" aria-hidden="true"></i> Region:   </b> SW </p>

                            </div>



                            <div class="row" style="clear: both">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Uploaded list</h3>


                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive no-padding">
                                        <table class="table table-hover" style="font-size:16px">
                                            <tr>
                                                <th>ID</th>
                                                <th>Media Station</th>
                                                <th>Time</th>
                                                <th>Duration</th>
                                                <th>Amount</th>
                                                <th>Action</th>

                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td> <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="5%"> NTA  </td>
                                                <td>9:30 - 9:33 am</td>
                                                <td>15 seconds</td>
                                                <td>11,000</td>
                                                <td><a href="#" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash-o" aria-hidden="true"></i> Remove</span></a></td>

                                            </tr>

                                            <tr>
                                                <td>2</td>
                                                <td> <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="5%"> NTA  </td>
                                                <td>10:20 - 10:24 am</td>
                                                <td>15 seconds</td>
                                                <td>11,000</td>
                                                <td><a href="#" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash-o" aria-hidden="true"></i> Remove</span></a></td>

                                            </tr>



                                        </table>
                                        <h2 align="" style="padding:2%">TOTAL: N22,000</h2>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>

                        </div>


                </form>

            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-2 hidden-sm hidden-xs"></div>
        <!-- /.col -->




        </div>
        <!-- /.row -->

        <div class="container">

            <p align="right">
                <a href="create-campaign-page8.html"><button class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button></a>
                <button class="btn campaign-button" style="margin-right:15%" data-toggle="modal" data-target=".bs-example2-modal-lg" >Next <i class="fa fa-play" aria-hidden="true"></i></button>

            </p>
            <div class="modal fade bs-example2-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" style="padding: 5%">
                        <h2>Payment</h2>
                        <hr / style="border-bottom: 1px solid #eee">

                        for the time-slot bought for your adverts, the price is: <br />
                        <h3> Total: N 22,000 </h3>

                        Choose payment plab:
                        <form>
                            <input type="radio" name="payment" value="Cash" checked> Cash<br>
                            <input type="radio" name="payment" value="Payment"> Cash<br>
                            <input type="radio" name="payment" value="other"> Transfer
                        </form>
                        <p align="center">
                            <a href="#" style="color:white"><button  class="btn btn-large" style="background: #34495e; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Confirm payment</button></a></p>
                    </div>
                </div>
            </div>
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