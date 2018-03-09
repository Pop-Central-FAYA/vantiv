@extends('layouts.new_app')

@section('title')
    <title>Create Campaign</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Upload Media List</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Create Campaign</a></li>
                        <li><a href="#">Upload Media List</a></li>
                    </ul>
                </div>

                <div class="Add-brand">
                    <div class="row">
                        <div class="col-md-12 ">
                            <h2>
                                <p align="center">
                                    The history of advertising can be traced to ancient civilizations.
                                    It became a major force in capitalist economies in the mid-19th century,
                                    based primarily on newspapers and magazines. In the 20th century,
                                    advertising grew rapidly with new technologies such as direct mail, radio, television,
                                    the internet and mobile devices.
                                </p>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h3>Uploaded list</h3>

                        <table class="table table-bordered table-striped campaign">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>File Name</th>
                                    <th>Duration</th>
                                    {{--<th>Action</th>--}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($uploads as $upload)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ decrypt($upload->uploads) }}</td>
                                        <td>{{ $upload->time }}</td>
                                        {{--<td><button type="button" data-toggle="modal" data-target=".deleteModal{{ $upload->id }}" class="btn btn-danger btn-xs">Delete</button></td>--}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="Add-brand">
                    <div class="row">
                        <div class="col-md-12 ">
                            <form action="{{ route('campaign.create6', ['walkins' => $walkins]) }}" method="GET">
                                <div class="input-group">
                                    <p align="right">
                                        {{--<button type="button" id="step4" class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>--}}
                                        {{--<button type="submit" class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button>--}}
                                        <input type="Submit" name="Submit" value="Next" />
                                    </p>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    @foreach($uploads as $upload)
        <div class="modal fade deleteModal{{ $upload->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content" style="padding: 7%">

                    <div class="modal-header">
                        <h2 class="text-center">Are you sure you want to delete?</h2>
                    </div>
                    <div class="modal-body">
                        <h5>
                            <b style="color: red">Warning!!!</b>
                            Deleting this means you might not be able to fully undo this operation
                        </h5>
                    </div>
                    <div class="modal-footer">
                        <button  class="btn btn-large btn-danger" data-dismiss="modal" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Cancel</button>
                        <a href="{{ route('uploads.delete', ['walkins' => $walkins, 'id' => $upload->id]) }}" type="submit" class="btn btn-large btn-success" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Delete</a>
                    </div>

                </div>
            </div>
        </div>
    @endforeach





                    {{--<div class="row" style="margin-top:3%">--}}
                    {{--<form class="campform" method="POST" action="{{ route('campaign.store5.uploads', ['walkins' => $walkins]) }}" enctype="multipart/form-data">--}}
                        {{--{{ csrf_field() }}--}}
                        {{--<div class="col-md-6">--}}
                            {{--<div class="form-group">--}}
                                {{--<label>Upload Media</label>--}}
                                {{--<input type="file" id="fup" name="uploads">--}}
                                {{--<input type="hidden" class="form-control" name="f_du" id="f_du" size="5" />--}}
                            {{--</div>--}}

                            {{--<div class="form-group">--}}
                                {{--<label>Duration </label> <br />--}}
                                {{--<select style="width: 60%" name="time">--}}
                                    {{--<option value="15">15 Seconds</option>--}}
                                    {{--<option value="30">30 Seconds</option>--}}
                                    {{--<option value="45">45 Seconds</option>--}}
                                    {{--<option value="60">60 Seconds</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                            {{--<audio id="audio"></audio>--}}

                            {{--<button type="submit" class="btn campaign-button btn-xs" style="margin-right:15%">Upload</button>--}}
                        {{--</div>--}}


                    {{--</form>--}}

                        {{--<div class="col-md-6">--}}

                        {{--</div>--}}

                    {{--</div>--}}

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
            $('#step4').click(function(){
                window.location.href = "/campaign/create/1/step4";
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

@endsection
