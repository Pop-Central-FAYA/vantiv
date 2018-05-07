@extends('layouts.new_app')

@section('title')
    <title>Faya - Agency Dashboard</title>
@stop

@section('styles')

    <link rel="stylesheet" href="{{ asset('asset/dist/css/dashboard.css') }}" />

@endsection

@section('content')
    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>{{ Auth::user()->username }} Dashboard </h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Admin</a></li>
                        <li><a href="#">Dashboard </a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="metrics-widget">
                        <div class="metrics-widget-heading">
                            <h2>All Broadcasters</h2>
                        </div>
                        <hr/>
                        <div class="metrics-widget-value">
                            <h1>10</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metrics-widget">
                        <div class="metrics-widget-heading">
                            <h2>All Agencies</h2>
                        </div>
                        <hr/>
                        <div class="metrics-widget-value">
                            <h1>10</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metrics-widget">
                        <div class="metrics-widget-heading">
                            <p><h2>All Advertises</h2></p>
                        </div>
                        <hr/>
                        <div class="metrics-widget-value">
                            <h1>10</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metrics-widget">
                        <div class="metrics-widget-heading">
                            <p><h2>All Industries</h2></p>
                        </div>
                        <hr/>
                        <div class="metrics-widget-value">
                            <h1>50</h1>
                        </div>
                    </div>
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

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>



@stop