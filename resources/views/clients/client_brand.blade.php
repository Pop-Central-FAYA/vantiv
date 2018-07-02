@extends('layouts.faya_app')

@section('title')
    <title> FAYA | CLIENT'S BRAND-CAMPAIGN </title>
@stop

@section('content')
    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Clients</h2>
                <p class="bread small_font"><a href="{{ route('client.show', ['id' => $client_id]) }}">{{ $user_details[0]->firstname . ' ' . $user_details[0]->lastname }}</a> &raquo; <a href="">Brand</a> &raquo; <span class="weight_medium">{{ $this_brand[0]->name }}</span></p>
            </div>
        </div>


        <div class="the_frame client_dets mb4">


            <div class="tab_contain">
                <div class="tab_content" id="history">
                    @if(count($campaigns) === 0)
                        <p>No campaigns for this brand</p>
                    @else
                    <table>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Budget</th>
                            <th>Amount Spent</th>
                            <th>Media Plan</th>
                            <th>Status</th>
                            <th></th>
                            <th></th>
                        </tr>

                        @foreach($campaigns as $campaign)
                            <tr>
                                <th><input type="checkbox"></th>
                                <td>{{ $campaign['id'] }}</td>
                                <td>{{ $campaign['name'] }}</td>
                                <td>{{ $campaign['date_created'] }}</td>
                                <td>&#8358; {{ $campaign['budget'] }}</td>
                                <td>&#8358; {{ $campaign['budget'] }}</td>
                                @if($campaign['mpo_status'] === 1)
                                    <td class="color_base weight_medium">Approved</td>
                                @else
                                    <td class="weight_medium" style="color: red;">Pending</td>
                                @endif
                                <td>{{ $campaign['status'] }}</td>
                                <td><a href="">View MPO</a></td>
                                <td><a href="">View Invoice</a></td>
                            </tr>
                        @endforeach

                    </table>
                    @endif

                </div>
                <!-- end -->

            </div>
        </div>

    </div>
@stop



{{--@extends('layouts.new_app')--}}

{{--@section('title')--}}
    {{--<title>Agency | All Client's Brands</title>--}}
{{--@stop--}}

{{--@section('styles')--}}

    {{--<link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />--}}
    {{--<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">--}}

{{--@endsection--}}

{{--@section('content')--}}

    {{--<div class="main-section">--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-12 heading-main">--}}
                    {{--<h1>All Client's Brands</h1>--}}
                    {{--<ul>--}}
                        {{--<li><a href="{{ route('clients.list') }}"><i class="fa fa-edit"></i>All Clients</a></li>--}}
                    {{--</ul>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="row">--}}
                {{--<div class="col-md-12">--}}
                    {{--<div class="table-responsive">--}}
                        {{--<table class="table">--}}
                            {{--<thead>--}}
                            {{--<tr>--}}
                                {{--<th>S/N</th>--}}
                                {{--<th>Brands</th>--}}
                                {{--<th>No. of Campaigns</th>--}}
                                {{--<th>Brand Logo</th>--}}
                            {{--</tr>--}}
                            {{--</thead>--}}
                            {{--<tbody>--}}
                            {{--@foreach($brands as $brand)--}}
                                {{--<tr>--}}
                                    {{--<td>{{ $loop->iteration }}</td>--}}
                                    {{--<td>{{ ucfirst($brand['brand']) }}</td>--}}
                                    {{--<td>{{ $brand['campaigns'] }}</td>--}}
                                    {{--<td><img src="{{ $brand['image_url'] ? asset(decrypt($brand['image_url'])) : '' }}" class="img-circle img-responsive" alt=""></td>--}}
                                {{--</tr>--}}
                            {{--@endforeach--}}
                            {{--</tbody>--}}
                        {{--</table>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

{{--@stop--}}

{{--@section('scripts')--}}

    {{--{!! HTML::script('assets/js/moment.min.js') !!}--}}
    {{--{!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}--}}
    {{--{!! HTML::script('assets/js/as/profile.js') !!}--}}

{{--@stop--}}