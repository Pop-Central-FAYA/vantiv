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
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Advertiser</a></li>
                        <li><a href="{{ route('campaign.all') }}">All Campaign</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="Create-campaign">

                    <h2>Choose Media: Upload Stage for 45 seconds slot</h2>
                    <hr>
                    <p><br></p>

                    <div class="col-md-3 ">
                        <form class="campform dropzone" id="upload1"  method="POST" action="{{ route('campaign.store4_2', ['walkins' => $walkins]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                        </form>

                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            @include('partials.show_file')
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="container">

                    <p align="right">
                    <form class="campform"  method="POST" action="{{ route('campaign.store4_2', ['walkins' => $walkins]) }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
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
        Dropzone.options.upload1 = {
            maxFilesize: 50,
            acceptedFiles: 'video/*',
            maxFiles: 1,
            dictDefaultMessage: 'Click or drag your 45 Seconds video here for quick upload',
            init: function() {
                this.on("queuecomplete", function(file, serverresponse) { window.location.href="/campaign/create/"+"<?php echo $walkins; ?>"+"/step4/3"; });
            }
        };
    </script>

    <script>
        $(document).ready(function() {
            var user_id = "<?php echo $walkins ?>";
            $('#step3').click(function(){
                window.location.href = '/campaign/create/1/'+user_id+'/step3';
            });
        });
    </script>

@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('dropzone.css') }}">
@stop
