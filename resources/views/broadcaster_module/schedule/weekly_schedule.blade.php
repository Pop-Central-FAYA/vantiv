@extends('layouts.ssp.layout')

@section('title')
    <title>Torch | Schedule</title>
@stop

@section('content')
    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')
    <!-- main container -->
    <div class="main_contain" id="app">
        <!-- header -->
        @include('partials.new-frontend.broadcaster.header')
        <div class="container-fluid media-asset-management">
            <schedule-mpo-filter
            :mpos="{{ json_encode($mpos) }}"
            ></schedule-mpo-filter>
            <weekly-schedule
                :time_belts="{{ json_encode($time_belts) }}"
                :weekly_schedule="{{ json_encode($schedules) }}"
                :ad_pattern="{{ $ad_pattern }}"
            ></weekly-schedule>
        </div>
    </div>
@stop

@section('scripts')
    <!-- App.js -->
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
@stop

@section('styles')
    <link href="{{ asset('new_frontend/css/custom-calendar.css') }}" rel="stylesheet">
@stop
