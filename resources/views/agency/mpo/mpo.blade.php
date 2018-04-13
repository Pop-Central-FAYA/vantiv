@extends('layouts.new_app')

@section('title')
    <title>Agency - Campaign-Lists</title>
@stop

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>MPO details</h1>
                    <ul>
                        <li><a href="{{ route('agency.campaign.all') }}"><i class="fa fa-th-large"></i>All MPO</a></li>
                        <li><a href="#">MPO</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <p><h4>The Commercial Manager</h4></p>
                    <p><h3>{{ $mpo_details['agency'] }}</h3></p>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <p><h4>Client: {{ $mpo_details['clients'] }}</h4></p>
                    <p><h4>Brand: {{ $mpo_details['brand'] }}</h4></p>
                    <p><h4>Campaign: {{ $mpo_details['campaign'] }}</h4></p>
                    <p><h4>Date: {{ $mpo_details['date'] }}</h4></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <p><b><h3 class="text-center">Revised Media Contract</h3></b></p>
                    <p> <b><h4 class="text-center"> No: RMC {{ $mpo_details['invoice_number'] }}</h4></b></p>
                </div>
            </div>
            <p><br></p>
            <p><br></p>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Year</th>
                                <th>Media</th>
                                <th>Specification</th>
                                <th>Spots</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mpo_details['mpo'] as $all_mpo)
                                <tr>
                                    <td>{{ $all_mpo['year'] }}</td>
                                    <td>{{ $all_mpo['media'] }}</td>
                                    <td></td>
                                    <td>{{ $all_mpo['spot'] }}</td>
                                    <td>{{ $all_mpo['total'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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

@stop