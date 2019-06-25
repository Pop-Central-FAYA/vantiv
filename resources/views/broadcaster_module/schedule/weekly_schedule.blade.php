@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Schedule</title>
@stop

@section('content')
    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')
    <!-- main container -->
    <div class="main_contain" id="app">
        <!-- header -->
        @include('partials.new-frontend.broadcaster.header')

        <div class="container-fluid media-asset-management">
            <div class="row">
                <!-- subheader -->
                <div class="col-md-6">
                    <h2 class="sub_header">Weekly Schedule</h2>
                </div>
            </div>
            <div class="row my-5">
                <vue-cal-weekly-schedule></vue-cal-weekly-schedule>

            </div>
        </div>
    </div>
@stop

@section('scripts')
    <!-- App.js -->
    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
@stop