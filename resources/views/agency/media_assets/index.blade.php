@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Media Assets</title>
@stop

@section('styles')
    <!-- App.js -->
    <!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css" rel="stylesheet"> -->
@stop

@section('content')

    <!-- main container -->
    <div class="main_contain">
        <!-- header -->
        @include('partials.new-frontend.agency.header')

        <div class="container-fluid media-asset-management">
            <div class="row">
                <!-- subheader -->
                <div class="col-md-6">
                    <h2 class="sub_header">Media Assets</h2>
                </div>
                <div class="col-md-6 text-right">
                    <media-asset-upload :clients="{{ json_encode($clients) }}"></media-asset-upload>
                </div>
            </div>
            <div class="row my-5">
                <div class="col-md-12">
                <v-app>
                    <v-content>
                    <media-asset-display></media-asset-display>
                    </v-content>
                </v-app>
            </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <!-- App.js -->
    <script src="{{ asset('js/app.js') }}"></script>
@stop