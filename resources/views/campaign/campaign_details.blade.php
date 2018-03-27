@extends('layouts.new_app')

@section('title')
    <title>Campaign Details</title>
@endsection


@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Campaign Details</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Broadcaster</a></li>
                        <li><a href="#">Campaign Details</a></li>
                    </ul>
                </div>
            </div>
                {{--{{ dd($campaign_details) }}--}}
            <div class="row">
                <div class="col-md-6">
                    <h2>Campaign Information</h2>
                    <hr>

                    <p><h4>Campaign Name: {{ $campaign_details['campaign_det']['campaign_name'] }} </h4></p><br>
                    <p><h4>Product: {{ $campaign_details['campaign_det']['product_name'] }} </h4></p><br>
                    <p><h4>Brand: {{ $campaign_details['campaign_det']['brand'] }}</h4></p><br>
                    @if(!Session::get('broadcaster_id'))
                        <p><h4>Medium: {{ $campaign_details['campaign_det']['channel'] }}</h4></p><br>
                    @endif
                    <p><h4>Start Date: {{ $campaign_details['campaign_det']['start_date'] }}</h4></p><br>
                    <p><h4>End Date: {{ $campaign_details['campaign_det']['end_date'] }}</h4></p><br>
                    <p><h4>Cost Budget: &#8358;{{ $campaign_details['campaign_det']['campaign_cost'] }}</h4></p><br>
                </div>
                <div class="col-md-6">
                    <h2>Walk-In Information</h2>
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
                                <th>Day</th>
                                <th>From-To Time</th>
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
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h2>File Details</h2>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>File</th>
                                <th>File Duration</th>
                                <th>From to Time</th>
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
                                    <td>{{ $file_detail['slot_time'] }}</td>
                                    <td>{{ $file_detail['from_to_time'] }}</td>
                                    <td>Played</td>
                                    <td>@if($file_detail['file_status'] === 1) File Approved @elseif($file_detail['file_status'] === 2) File Rejected. With reason : <strong>{{ $file_detail['rejection_reason'] }}</strong>  @else Pending @endif</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}



@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop