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
            </div>
            <div class="row">
                <div class="Create-campaign">

                    <h2>Choose Media: Upload Stage for 30 seconds slot</h2>
                    <hr>
                    <p><br></p>

                    <div class="col-md-3 ">
                        <form class="campform dropzone" id="upload1"  method="POST" action="{{ route('advertiser_campaign.store3_1', ['id' => $id, 'broadcaster' => $broadcaster]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                        </form>

                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            @include('show_file_tv.blade.php')
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="container">

                    <p align="right">
                    <form class="campform"  method="POST" action="{{ route('advertiser_campaign.store3_1', ['id' => $id, 'broadcaster' => $broadcaster]) }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <button type="submit" class="btn campaign-button btn-danger btn-lg" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button>
                    </form>
                    </p>
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
    <script src="{{ asset('dropzone.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('agency_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script>
        Dropzone.options.upload1 = {
            maxFilesize: 50,
            acceptedFiles: 'video/*',
            maxFiles: 1,
            dictDefaultMessage: 'Click or drag your 30 Seconds video here for quick upload',
            addRemoveLink: true,
            init: function () {
                this.on("complete", function (file, res) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0 && file.status === Dropzone.SUCCESS) {
                        toastr.success('File Uploaded successfully');
                        window.location.href="/advertiser/campaigns/campaign/step3/2/"+"<?php echo $id ?>"+"/"+"<?php echo $broadcaster ?>";

                    } else {
                        toastr.error('Something went wrong with your upload');
                        return;
                    }
                });
            }
        };
    </script>


@stop
@section('styles')
    <link rel="stylesheet" href="{{ asset('dropzone.css') }}">
@stop

