@extends('layouts.new_app')

@section('title')
    <title>Create Campaign</title>
@endsection

@section('content')
    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Create Campaigns</h1>
                    <ul>
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Broadcaster User</a></li>
                        <li><a href="{{ route('campaign.all') }}">All Campaign</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="Create-campaign">

                    <h2>Choose Media</h2>
                    <hr>

                    <p><br></p>

                    <div class="row">
                        <form method="POST" action="{{ route('broadcaster.user.campaign.store3', ['walkins' => $walkins, 'broadcaster' => $broadcaster, 'broadcaster_user' => $broadcaster_user]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-md-3">
                                <select class="form-control" name="time" id="">
                                    <option value="15">15 Seconds</option>
                                    <option value="30">30 Seconds</option>
                                    <option value="45">45 Seconds</option>
                                    <option value="60">60 Seconds</option>
                                </select>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-3">
                                <input type="file" class="form-control" id="fup" name="uploads">
                                <input type="hidden" name="f_du" id="f_du" size="5" />
                            </div>
                            <div class="col-md-1"></div>

                            <audio id="audio"></audio>
                            <div class="col-md-3">
                                <button class="btn btn-success" type="submit">Upload</button>
                            </div>
                        </form>
                    </div>
                    <p><br></p>
                    @include('partials.show_file')
                </div>
            </div>
            <p><br></p>
            <p><br></p>
            <p><br></p>

            <div class="row">
                <div class="container">
                    <p align="right">
                    <form class="campform"  method="POST" action="{{ route('broadcaster.user.campaign.store3_1', ['walkins' => $walkins, 'broadcaster' => $broadcaster, 'broadcaster_user' => $broadcaster_user]) }}" >
                        {{ csrf_field() }}
                        <button type="button" id="step3" class="btn campaign-button btn-danger btn-lg" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>
                        <button type="submit" class="btn campaign-button btn-danger btn-lg" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button>
                    </form>
                    </p>
                </div>
            </div>

        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{ asset('asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js' }}"></script>
    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <!-- bootstrap time picker -->
    <script src="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('asset/plugins/iCheck/icheck.min.js') }}"></script>

    <script src="{{ asset('dropzone.js') }}"></script>
    <script>
        $(document).ready(function() {
            var user_id = "<?php echo $walkins ?>";
            var broadcaster = "<?php echo $broadcaster ?>";
            var broadcaster_user = "<?php echo $broadcaster_user ?>";
            $('#step3').click(function(){
                window.location.href = '/broadcaster-user/campaign/create/'+user_id+'/'+broadcaster+'/'+broadcaster_user+'/step2';
            });
        });
    </script>

    <script>

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
    <script>
        $(document).ready(function() {
            var user_id = "<?php echo $walkins ?>";
            var broadcaster = "<?php echo $broadcaster ?>";
            var broadcaster_user = "<?php echo $broadcaster_user ?>";
            $('#step2').click(function(){
                window.location.href = '/broadcaster-user/campaign/create/'+user_id+'/'+broadcaster+'/'+broadcaster_user+'/step2';
            });
        });
    </script>


@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('dropzone.css') }}">
@stop