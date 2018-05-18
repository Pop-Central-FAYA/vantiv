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
                        <li><a href="{{ route('agency.campaign.all') }}">All Campaign</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="Create-campaign">

                    <h2>Choose Media</h2>
                    <hr>

                    <p><br></p>

                    <div class="row">
                        <form method="POST" action="{{ route('advertiser_campaign.store3', ['id' => $id]) }}" enctype="multipart/form-data">
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
                    <form class="campform"  method="POST" action="{{ route('advertiser_campaign.store3_1', ['id' => $id]) }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <button type="button" style="background: #00c4ca" id="step2" class="btn campaign-button btn-danger btn-lg" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>
                        <button type="submit" class="btn campaign-button btn-danger btn-lg" style="background:#00c4ca; margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button>
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

@stop

