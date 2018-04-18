@extends('layouts.new_app')

@section('title')
    <title>Agency - Campaign-Details</title>
@stop

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Campaign Details</h1>
                    <ul>
                        <li><a href="{{ route('agency.campaign.all') }}"><i class="fa fa-th-large"></i>Agency Campaign</a></li>
                        <li><a href="#">Campaign Details</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h2>Campaign Information</h2>
                    <hr>

                    <p><h4>Campaign Name: {{ $campaign_details['campaign_det']['campaign_name'] }} </h4></p><br>
                    <p><h4>Product: {{ $campaign_details['campaign_det']['product_name'] }} </h4></p><br>
                    <p><h4>Brand: {{ $campaign_details['campaign_det']['brand'] }}</h4></p><br>
                    <p><h4>Channel: {{ $campaign_details['campaign_det']['channel'] }}</h4></p><br>
                    <p><h4>Start Date: {{ $campaign_details['campaign_det']['start_date'] }}</h4></p><br>
                    <p><h4>End Date: {{ $campaign_details['campaign_det']['end_date'] }}</h4></p><br>
                    <p><h4>Campaign Budget: &#8358;{{ $campaign_details['campaign_det']['campaign_cost'] }}</h4></p><br>
                </div>
                <div class="col-md-6">
                    <h2>Clients Information</h2>
                    <hr>
                    <p><h4>Name: {{ $campaign_details['campaign_det']['walkIn_name'] }}</h4></p><br>
                    <p><h4>E-mail: {{ $campaign_details['campaign_det']['email'] }}</h4></p><br>
                    <p><h4>Phone: {{ $campaign_details['campaign_det']['phone'] }}</h4></p><br>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h2>Adslot Details</h2>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Media Station</th>
                                <th>Day</th>
                                <th>From To Time</th>
                                <th>Day-parts</th>
                                <th>Target Audience</th>
                                <th>Region</th>
                                <th>Min Age</th>
                                <th>Max Age</th>
                                <th>Hourly Range</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($campaign_details['file_details'] as $file_detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $file_detail['broadcast_station'] }}</td>
                                    <td>{{ $file_detail['day'] }}</td>
                                    <td>{{ $file_detail['from_to_time'] }}</td>
                                    <td>{{ $file_detail['day_part'] }}</td>
                                    <td>{{ $file_detail['target_audience'] }}</td>
                                    <td>{{ $file_detail['region'] }}</td>
                                    <td>{{ $file_detail['minimum_age'] }}</td>
                                    <td>{{ $file_detail['maximum_age'] }}</td>
                                    <td>{{ $file_detail['hourly_range'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <h2>File Details</h2>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>File</th>
                                <th>Media Station</th>
                                <th>File Duration</th>
                                <th>From To Time</th>
                                <th>Play Time Status</th>
                                <th>File Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($campaign_details['file_details'] as $file_detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <video src="{{ $file_detail['file'] }}" width="150" height="100" controls></video>
                                    </td>
                                    <td>{{ $file_detail['broadcast_station'] }}</td>
                                    <td>{{ $file_detail['slot_time'] }}</td>
                                    <td>{{ $file_detail['from_to_time'] }}</td>
                                    <td>Pending to Play</td>
                                    <td>@if($file_detail['file_status'] === 1) File Approved @elseif($file_detail['file_status'] === 2) File Rejected. With reason : <strong>{{ $file_detail['rejection_reason'] }}</strong> @else Pending Approval @endif</td>
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